/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var hashchange='';
var mydata = {};
var from_php_user_settings_autocomplete_address='from_googlemaps';



var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;


var place_map_latitude=0;
var place_map_longitude=0;
var map_is_open=false; 
var map;
var markerpos;
var myLatLng;
var measureTool;
var infoWindow_userpos=null;
var gks_bounds_local=null;
var appmobile_last_id_gps = new Map();

jQuery(document).ready(function($) {

  // hash 
  var panel_tool_selected='users';


  $(window).hashchange( function(){
    currsearch=window.location.hash.replace('#', '');
    if (hashchange == currsearch) return;
    currsearch=decodeURI(currsearch);
    //console.log('window hashchange');
    //console.log(currsearch);
    
    hash_analyze(currsearch);
    //get_data();
  });

  function hash_analyze(currsearch) {
    if (currsearch!='') {
      try {
        mydata = JSON.parse(currsearch);
  
      } catch(err) {
        //console.log('error ' + err);  
        return;
      }
    } 
    //console.log(mydata); 
    

    
    if (mydata.zoom === undefined || mydata.zoom  === null) mydata.zoom=17;
    if (mydata.mlat === undefined || mydata.mlat  === null) mydata.mlat=0;
    if (mydata.mlng === undefined || mydata.mlng  === null) mydata.mlng=0;
    if (mydata.lat === undefined || mydata.lat  === null) mydata.lat=0;
    if (mydata.lng === undefined || mydata.lng  === null) mydata.lng=0;

    
    if (mydata.panel === undefined || mydata.panel  === null) mydata.panel=panel_tool_selected;
    if (mydata.users === undefined || mydata.users  === null) mydata.users=false;
    if (mydata.appmobile === undefined || mydata.appmobile  === null) mydata.appmobile=false;
    if (mydata.lead === undefined || mydata.lead  === null) mydata.lead=false;
    if (mydata.calendar === undefined || mydata.calendar  === null) mydata.calendar=false;
    if (mydata.task === undefined || mydata.task  === null) mydata.task=false;
    if (mydata.machine === undefined || mydata.machine  === null) mydata.machine=false;
    if (mydata.poi === undefined || mydata.poi  === null) mydata.poi=false;
    

    
    
    if (mydata.lat!=0 || mydata.lng!=0) {
      place_map_latitude=mydata.lat;
      place_map_longitude=mydata.lng;      
      $('#poi_map_latitude').val(place_map_latitude);
      $('#poi_map_longitude').val(place_map_longitude);
    }
    
    
    if (mydata.users && $("#users_enable").is(':checked')==false) $("#users_enable").click();
    if (mydata.appmobile && $("#appmobile_enable").is(':checked')==false) $("#appmobile_enable").click();
    if (mydata.lead && $("#lead_enable").is(':checked')==false) $("#lead_enable").click();
    if (mydata.calendar && $("#calendar_enable").is(':checked')==false) $("#calendar_enable").click();
    if (mydata.task && $("#task_enable").is(':checked')==false) $("#task_enable").click();
    if (mydata.machine && $("#machine_enable").is(':checked')==false) $("#machine_enable").click();
    if (mydata.poi && $("#poi_enable").is(':checked')==false) $("#poi_enable").click();
    



    
    panel_tool_selected=mydata.panel;
    
    //console.log('hash_analyze');
    //console.log(mydata);
    
  }
  function set_hash() {
    if (gks_page_loading) return;
    //console.log('set_hash');
    mydata={};
    mydata.zoom=map.getZoom();
    mydata.mlat=map.getCenter().lat();
    mydata.mlng=map.getCenter().lng();
    
    mydata.lat=place_map_latitude;
    mydata.lng=place_map_longitude;

    mydata.panel=panel_tool_selected;
    mydata.users=$("#users_enable").is(':checked');
    mydata.appmobile=$("#appmobile_enable").is(':checked');
    mydata.lead=$("#lead_enable").is(':checked');
    mydata.calendar=$("#calendar_enable").is(':checked');
    mydata.task=$("#task_enable").is(':checked');
    mydata.machine=$("#machine_enable").is(':checked');
    mydata.poi=$("#poi_enable").is(':checked');
    
    
    
    //console.log('set_hash');
    //console.log(mydata);
    new_hash=encodeURI(JSON.stringify(mydata));
    if (new_hash!=hashchange) document.location.hash = new_hash;
    hashchange=new_hash;
  }

  function get_data() {
    set_hash();
    
    
  }

  currsearch=window.location.hash.replace('#', '');
  currsearch=decodeURI(currsearch);
  hash_analyze(currsearch);
  //get_data();
  
  function myresize() {
    mywidth=$(window).width();
    myheight=$(window).height();
    data_s=$('#button_full').attr('data-s');
    if (data_s=='0') {
      header_height=$('gks_nav_parent').outerHeight();
      footer_height=$('#gks_nav_session_footer').outerHeight();
      myh=myheight - header_height - footer_height;
      $('#gks_map_div_main').css('height',myh + 'px');
    } else {
      myh=myheight;
      $('#gks_map_div_main').css('height',myh + 'px');
    }
    //console.log(myh);
  }
  $(window).resize(myresize);
  myresize();
  
  

  $('#button_full').click(function() {
    data_s=$(this).attr('data-s');
    if (data_s=='0') {
      $('gks_nav_parent').hide();
      $('#gks_nav_session_footer').hide();
      $(this).attr('data-s','1'); 
      $(this).find('span').removeClass('fa-expand').addClass('fa-compress');
    } else {
      $('gks_nav_parent').show();
      $('#gks_nav_session_footer').show();
      $(this).attr('data-s','0');
      $(this).find('span').removeClass('fa-compress').addClass('fa-expand');
    }
    myresize();
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
    
  $('#poi_map_latitude, #poi_map_longitude').on('change keyup paste', function() {
    place_map_latitude=parseFloat($('#poi_map_latitude').val()); if (isNaN(place_map_latitude)) place_map_latitude=0;
    place_map_longitude=parseFloat($('#poi_map_longitude').val()); if (isNaN(place_map_longitude)) place_map_longitude=0;
    myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
    //markerpos.setOptions({position: myLatLng});
    markerpos.position=myLatLng;
    map.setOptions({center: myLatLng});
    //map.setOptions({zoom: 17});
    set_hash();
    
  }); 

  $('#map_pos').click(function(event){
    
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        map.setCenter(pos);
        //markerpos.setOptions({position: pos});
        markerpos.position=pos;
        place_map_latitude = markerpos.position.lat;
        place_map_longitude = markerpos.position.lng;
        infoWindow_userpos.open(map, markerpos);
        map.setZoom(17);
          
        $('#poi_map_latitude').val(place_map_latitude);
        $('#poi_map_longitude').val(place_map_longitude);
      }, function() {
        map.setOptions({zoom: 17});
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      map.setOptions({zoom: 17});
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });
  


  
  $('#close_text_search_auto_googlemaps').click(function() {
    $('#text_search_auto_googlemaps').slideUp();  
  });

  $('#button_hide_left').click(function() {
    data_s=$(this).attr('data-s');
    if (data_s=='0') {
      $('#gks_map_div_panel_left').hide();
      $(this).attr('data-s','1'); 
      $('#gks_map_div_panel_map').addClass('gks_map_div_panel_map_full');
      
      $(this).find('span').attr('class','fas fa-columns');
    } else {
      $('#gks_map_div_panel_left').show();
      $(this).attr('data-s','0');
      $('#gks_map_div_panel_map').removeClass('gks_map_div_panel_map_full');
      $(this).find('span').attr('class','far fa-window-maximize');
    }
    myresize();
  });

  var m_points = new Map();

  var mygetdata_xhr;
  var mygetdata_xhr_running=false;
  
  var icon_template=`
  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 32 42">
    <path fill="#EA4335" stroke="#C5221F" stroke-width="1"
      d="M16 0C9.4 0 4 5.4 4 12c0 8.4 12 24 12 24s12-15.6 12-24c0-6.6-5.4-12-12-12z"/>
    <foreignObject x="8" y="4" width="16" height="16">
      <div xmlns="http://www.w3.org/1999/xhtml"
           style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;color:white;font-size:15px;">
        [[1]]
      </div>
    </foreignObject>
  </svg>`;
  
  function mygetdata(mybounds,only_mobile) {
    gks_bounds_local=mybounds;
    set_hash();
    
    //console.log('mybounds',mybounds);
    
    if (mybounds['north'].myround(1)==0 && mybounds['south'].myround(1)==0 && mybounds['east'].myround(1)==0 && mybounds['west'].myround(1)==0) {
      return;  
    }
    mygetdata_xhr_running=true;
    
    //console.log('mybounds',mybounds);
    
    if(mygetdata_xhr && mygetdata_xhr.readyState != 4){
      mygetdata_xhr.abort();
    }
    
    
    datasend='&mybounds_str=' + encodeURIComponent($.base64.encode(JSON.stringify(mybounds)));
    datasend+='&users=' + ($('#users_enable').is(':checked') ? 1 : 0);
    datasend+='&appmobile=' + ($('#appmobile_enable').is(':checked') ? 1 : 0);
    datasend+='&lead=' + ($('#lead_enable').is(':checked') ? 1 : 0);
    datasend+='&calendar=' + ($('#calendar_enable').is(':checked') ? 1 : 0);
    datasend+='&task=' + ($('#task_enable').is(':checked') ? 1 : 0);
    datasend+='&machine=' + ($('#machine_enable').is(':checked') ? 1 : 0);
    datasend+='&poi=' + ($('#poi_enable').is(':checked') ? 1 : 0);
    datasend+='&only_mobile=' + (only_mobile ? 1 : 0);
    
    temp=[];
    for ([key, value] of appmobile_last_id_gps) {
      temp.push({'i': key,'m': appmobile_last_id_gps.get(key)});
    }
    //console.log(temp);
    datasend+='&last_id_gps_str=' + encodeURIComponent($.base64.encode(JSON.stringify(temp)));
    

    $('#mygetdata_hourglass').show();
    mygetdata_xhr = $.ajax({
      url: '/my/admin-map-get-data.php',
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      gks_only_mobile: only_mobile,
      error : function(jqXHR ,textStatus,  errorThrown) {
        if (textStatus != 'abort') {
          myalert('error:' + jqXHR.responseText);
          $('#mygetdata_hourglass').hide();
        }
        mygetdata_xhr_running=false;
      },        
      success: function(data) {
        need_save=true;
        $('#mygetdata_hourglass').hide();
        if (!data) {
          myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            //console.log(data.data_appmobile);
            for ([key, value] of m_points) m_points.get(key).fordelete=true;
            
            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
              html_users='';
              for (i=0; i< data.data_users.length; i++) {
                mykey=data.data_users[i].obj + '_' + data.data_users[i].id;
                if (m_points.has(mykey)==false) {
                  mylabel={
                    text: ' ',
                    className: 'fas fa-user',
                    fontFamily: '',
                    color: "#ffffff",
                    fontSize: "12px",
                  };
                 
                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_users[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_users[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-user"></i>');
                                                      
                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_users[i].point.lat, lng: data.data_users[i].point.lng},
                    title: data.data_users[i].descr,
                    content: myelement,
                  });
                  
                  m_point={
                    obj: data.data_users[i].obj,
                    id: data.data_users[i].id,
                    type_id: 0,
                    marker: m_marker,
                    pos: {lat: data.data_users[i].point.lat, lng: data.data_users[i].point.lng},
                    title: gks_lang('Επαφή'),
                    descr: data.data_users[i].descr,
                    icon: '<i class="fas fa-user"></i>',
                    fordelete: false,
                    areas: [],
                  };
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_users[i].obj + '" ' +
                'data-iid="' + data.data_users[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_users[i].obj=='users') {
                  html_item+='admin-users-item.php?id=';
                } 
                
                html_item+=data.data_users[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-user"></i>' + ' ' + data.data_users[i].descr + '</div>';
                html_item+='</div>';
  
                html_users+=html_item;
              }
            }

            //////////////////////////////////////////////////////////////////
            html_appmobile='';
            
            for (var i in data.data_appmobile) {
              if (appmobile_last_id_gps.has(data.data_appmobile[i].id)) {
                if (data.data_appmobile[i].max_id_gps>appmobile_last_id_gps.get(data.data_appmobile[i].id)) {
                  //appmobile_last_id_gps.get(data.data_appmobile[i].id)=data.data_appmobile[i].max_id_gps;
                  appmobile_last_id_gps.set(data.data_appmobile[i].id,data.data_appmobile[i].max_id_gps);
                }
              } else {
                appmobile_last_id_gps.set(data.data_appmobile[i].id,data.data_appmobile[i].max_id_gps);
              }
              
              mykey=data.data_appmobile[i].obj + '_' + data.data_appmobile[i].id;
              if (m_points.has(mykey)==false) {
                mylabel={
                  text: ' ',
                  className: 'fas fa-mobile-alt',
                  fontFamily: '',
                  color: "#ffffff",
                  fontSize: "12px",
                };

                myelement = document.createElement('div');
                myelement.className = 'marker_adv_div';
                myelement.setAttribute('data-m-obj', data.data_appmobile[i].obj);
                myelement.setAttribute('data-m-iid', data.data_appmobile[i].id);
                myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-mobile-alt"></i>');

                m_marker = new google.maps.marker.AdvancedMarkerElement({
                  map: map,
                  position: {lat: data.data_appmobile[i].point.lat, lng: data.data_appmobile[i].point.lng},
                  title: data.data_appmobile[i].descr,
                  content: myelement,
                });
                
                m_point={
                  obj: data.data_appmobile[i].obj,
                  id: data.data_appmobile[i].id,
                  type_id: 0,
                  marker: m_marker,
                  pos: {lat: data.data_appmobile[i].point.lat, lng: data.data_appmobile[i].point.lng},
                  title: 'gks ERP App Mobile',
                  descr: data.data_appmobile[i].descr,
                  icon: '<i class="fas fa-mobile-alt"></i>',
                  fordelete: false,
                  areas: [],
                  paths: new Map()
                };
                
                m_points.set(mykey,m_point);
                
              } else {
                m_points.get(mykey).fordelete=false;
              }

              elem_item=$('#list_appmobile .list_item[data-iid=' + data.data_appmobile[i].id + ']');
              if (elem_item.length==0) {
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_appmobile[i].obj + '" ' +
                'data-iid="' + data.data_appmobile[i].id + '" ' +
                '><div class="list_itemd1"><span class="aa">--</span>. ' + 
                '<a href="';
                if (data.data_appmobile[i].obj=='appmobile') {
                  html_item+='admin-erp-app-mobile-item.php?id=';
                } 
                
                html_item+=data.data_appmobile[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-mobile-alt"></i>' + ' ' + data.data_appmobile[i].descr + '</div>';
                
                html_item+='<div class="list_path2"></div>';
                
                html_item+='</div>';
                
                $('#list_appmobile').append(html_item);
                $('#list_appmobile .list_item[data-iid=' + data.data_appmobile[i].id + ']').click(list_item_click);

                var mob_aa=0;
                $('#list_appmobile .list_item .aa').each(function() {
                  mob_aa++;
                  $(this).html(mob_aa);
                });
                

                
                
                
              } else {
                //exist
              }
              
              
              
              
              
              if (m_points.has(mykey)) {
                //console.log(m_points);  
                
                m_points.get(mykey).pos= {lat: data.data_appmobile[i].point.lat, lng: data.data_appmobile[i].point.lng};
                m_points.get(mykey).marker.position= {lat: data.data_appmobile[i].point.lat, lng: data.data_appmobile[i].point.lng};
                
                //paths
                for (var pp in data.data_appmobile[i].paths) {
                  if (m_points.get(mykey).paths.has(pp)==false) {
                    
                    
                    mypath={};
                    mypath.mydiadromi=pp;
                    mypath.num_points=data.data_appmobile[i].paths[pp].length;
                    //mypath.points=data.data_appmobile[i].paths[pp];
                    
                    mypath.gpath = new google.maps.Polyline({
                      path: data.data_appmobile[i].paths[pp],
                      geodesic: true,
                      strokeColor: "#FF0000",
                      strokeOpacity: 1.0,
                      strokeWeight: 2,
                    });
                    mypath.gpath.setMap(map);
                    
                    m_points.get(mykey).paths.set(pp,mypath);
                    
                    
                    parts=data.data_appmobile[i].paths_start[pp].split('-');
                    time_start=parts[2]+'/'+parts[1]+'/'+parts[0];
                    time_start+=' ' + parts[3]+':'+parts[4]+':'+parts[5];
                    d_1=Date.parse(parts[0]+'-'+parts[1]+'-'+parts[2]+'T'+parts[3]+':'+parts[4]+':'+parts[5]+'.000Z');
                    parts=data.data_appmobile[i].paths_end[pp].split('-');
                    d_2=Date.parse(parts[0]+'-'+parts[1]+'-'+parts[2]+'T'+parts[3]+':'+parts[4]+':'+parts[5]+'.000Z');
                    //console.log(d_1,d_2);
                    
                    diafora=mapDateDiff(d_2-d_1);
                    //console.log(diafora)
                    
                    path_html='<div class="list_path3" data-dname="' + pp + '" data-time="' + data.data_appmobile[i].paths_start[pp] + '">' +
                              '<span class="list_itemd41" title="'+gks_lang('Έναρξη')+'">' + time_start + '</span>' +
                              '<span class="list_itemd42" title="' + diafora.s + '" data-time="' + data.data_appmobile[i].paths_end[pp] + '">' + diafora.n + '</span>' +
                              '<span class="list_itemd43" title="'+gks_lang('Πλήθος σημείων')+'">' + data.data_appmobile[i].paths[pp].length + '</span>' +
                              '</div>';
                    
                    $('.list_item[data-obj=appmobile][data-iid=' + data.data_appmobile[i].id +'] .list_path2').append(path_html);
                    
                    $('.list_item[data-obj=appmobile][data-iid=' + data.data_appmobile[i].id +'] .list_path2 .list_path3[data-dname=' + pp + ']').click(list_path3_click);
                    
                  } else {
                    for (pa=0; pa< data.data_appmobile[i].paths[pp].length; pa++) {
                      //ppp=new google.maps.LatLng({lat: data.data_appmobile[i].paths[pp][pa].lat, lng: data.data_appmobile[i].paths[pp][pa].lng});
                      //const pppath=m_points.get(mykey).paths.get(pp).gpath.getPath();
                      //pppath.push(ppp);
                      
                      m_points.get(mykey).paths.get(pp).gpath.getPath().push(
                        new google.maps.LatLng({lat: data.data_appmobile[i].paths[pp][pa].lat, lng: data.data_appmobile[i].paths[pp][pa].lng})
                      );
                      
                      //m_points.get(mykey).paths.get(pp).gpath
                    }
                    ggg=m_points.get(mykey).paths.get(pp)
                    ggg.num_points+=data.data_appmobile[i].paths[pp].length;
                    m_points.get(mykey).paths.set(pp,ggg);
                    
                    $('.list_item[data-obj=appmobile][data-iid=' + data.data_appmobile[i].id +'] .list_path2 .list_path3[data-dname=' + pp + '] .list_itemd43').html(ggg.num_points);
                    
                    
                    parts=$('.list_item[data-obj=appmobile][data-iid=' + data.data_appmobile[i].id +'] .list_path2 .list_path3[data-dname=' + pp + ']').attr('data-time').split('-');
                    time_start=parts[2]+'/'+parts[1]+'/'+parts[0];
                    time_start+=' ' + parts[3]+':'+parts[4]+':'+parts[5];
                    d_1=Date.parse(parts[0]+'-'+parts[1]+'-'+parts[2]+'T'+parts[3]+':'+parts[4]+':'+parts[5]+'.000Z');
                    parts=data.data_appmobile[i].paths_end[pp].split('-');
                    d_2=Date.parse(parts[0]+'-'+parts[1]+'-'+parts[2]+'T'+parts[3]+':'+parts[4]+':'+parts[5]+'.000Z');
                    //console.log(d_1,d_2);
                    
                    diafora=mapDateDiff(d_2-d_1);
                    //console.log(diafora)
                    
                    $('.list_item[data-obj=appmobile][data-iid=' + data.data_appmobile[i].id +'] .list_path2 .list_path3[data-dname=' + pp + '] .list_itemd42').attr('data-time',data.data_appmobile[i].paths_end[pp]).html(diafora.n);
                    
                    
                    
                  }
                  vvvvv="ff";
                  
                }
                
              }
              
                           
              
              
              //html_appmobile+=html_item;
            }
            //console.log(appmobile_last_id_gps);


            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
              html_calendar='';
              for (i=0; i< data.data_calendar.length; i++) {
                mykey=data.data_calendar[i].obj + '_' + data.data_calendar[i].id;
                if (m_points.has(mykey)==false) {
                  mylabel={
                    text: ' ',
                    className: 'fas fa-calendar myborder3',
                    fontFamily: '',
                    color: "#ffffff",
                    fontSize: "12px",
                  };

                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_calendar[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_calendar[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-calendar myborder3" style="color:' + data.data_calendar[i].color + '"></i>');
                                  
                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_calendar[i].point.lat, lng: data.data_calendar[i].point.lng},
                    title:  data.data_calendar[i].descr,
                    content: myelement,
                  });
                  
                  m_point={
                    obj: data.data_calendar[i].obj,
                    id: data.data_calendar[i].id,
                    type_id: 0,
                    marker: m_marker,
                    pos: {lat: data.data_calendar[i].point.lat, lng: data.data_calendar[i].point.lng},
                    title: gks_lang('Ημερολόγιο','part2'),
                    descr: data.data_calendar[i].descr,
                    icon: '<i class="fas fa-calendar myborder3" style="color:' + data.data_calendar[i].color + '"></i>',
                    fordelete: false,
                    areas: [],
                  };
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_calendar[i].obj + '" ' +
                'data-iid="' + data.data_calendar[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_calendar[i].obj=='calendar') {
                  html_item+='admin-crm-calendar.php?id=';
                } 
                
                html_item+=data.data_calendar[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-circle myborder3" style="color:' + data.data_calendar[i].color + '"></i>' + ' ' + data.data_calendar[i].descr + '</div>';
                html_item+='</div>';
  
                html_calendar+=html_item;
              }
            }

                                                          
            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
              html_lead='';
              for (i=0; i< data.data_lead.length; i++) {
                mykey=data.data_lead[i].obj + '_' + data.data_lead[i].id;
                if (m_points.has(mykey)==false) {
                  mylabel={
                    text: ' ',
                    className: 'fas fa-circle myborder1',
                    fontFamily: '',
                    color: "#ffffff",
                    fontSize: "12px",
                  };

                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_lead[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_lead[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-circle myborder1" style="color:' + data.data_lead[i].color + '"></i>');

                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_lead[i].point.lat, lng: data.data_lead[i].point.lng},
                    title:  data.data_lead[i].descr,
                    content: myelement,
                  });
                  
                  m_point={
                    obj: data.data_lead[i].obj,
                    id: data.data_lead[i].id,
                    type_id: 0,
                    marker: m_marker,
                    pos: {lat: data.data_lead[i].point.lat, lng: data.data_lead[i].point.lng},
                    title: gks_lang('Ευκαιρία')+' / '+ data.data_lead[i].status,
                    descr: data.data_lead[i].descr,
                    icon: '<i class="fas fa-circle myborder1" style="color:' + data.data_lead[i].color + '"></i>',
                    fordelete: false,
                    areas: [],
                  };
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_lead[i].obj + '" ' +
                'data-iid="' + data.data_lead[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_lead[i].obj=='lead') {
                  html_item+='admin-crm-lead-item.php?id=';
                } 
                
                html_item+=data.data_lead[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-circle myborder1" style="color:' + data.data_lead[i].color + '"></i>' + ' ' + data.data_lead[i].descr + '</div>';
                html_item+='</div>';
  
                html_lead+=html_item;
              }
            }
            
            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
            html_task='';
              for (i=0; i< data.data_task.length; i++) {
                mykey=data.data_task[i].obj + '_' + data.data_task[i].id;
                if (m_points.has(mykey)==false) {
                  mylabel={
                    text: ' ',
                    className: 'fas fa-circle myborder2',
                    fontFamily: '',
                    color: "#ffffff",
                    fontSize: "12px",
                  };
                  
                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_task[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_task[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-circle myborder2" style="color:' + data.data_task[i].color + '"></i>');

                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_task[i].point.lat, lng: data.data_task[i].point.lng},
                    title:  data.data_task[i].descr,
                    content: myelement,
                  });
                  
                  m_point={
                    obj: data.data_task[i].obj,
                    id: data.data_task[i].id,
                    type_id: 0,
                    marker: m_marker,
                    pos: {lat: data.data_task[i].point.lat, lng: data.data_task[i].point.lng},
                    title: gks_lang('Εργασία')+' / ' + data.data_task[i].status,
                    descr: data.data_task[i].descr,
                    icon: '<i class="fas fa-circle myborder2" style="color:' + data.data_task[i].color + '"></i>',
                    fordelete: false,
                    areas: [],
                  };
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_task[i].obj + '" ' +
                'data-iid="' + data.data_task[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_task[i].obj=='task') {
                  html_item+='admin-crm-task-item.php?id=';
                } 
                
                html_item+=data.data_task[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-circle myborder2" style="color:' + data.data_task[i].color + '"></i>' + ' ' + data.data_task[i].descr + '</div>';
                html_item+='</div>';
  
                html_task+=html_item;
              }
            }
            
            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
              html_machine='';
              for (i=0; i< data.data_machine.length; i++) {
                mykey=data.data_machine[i].obj + '_' + data.data_machine[i].id;
                if (m_points.has(mykey)==false) {
                  mylabel={
                    text: ' ',
                    className: 'fas fa-hdd',
                    fontFamily: '',
                    color: "#ffffff",
                    fontSize: "12px",
                  };

                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_machine[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_machine[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]','<i class="fas fa-hdd"></i>');

                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_machine[i].point.lat, lng: data.data_machine[i].point.lng},
                    title: data.data_machine[i].descr,
                    content: myelement,
                  });
                  
                  m_point={
                    obj: data.data_machine[i].obj,
                    id: data.data_machine[i].id,
                    type_id: 0,
                    marker: m_marker,
                    pos: {lat: data.data_machine[i].point.lat, lng: data.data_machine[i].point.lng},
                    title: gks_lang('Συσκευή'),
                    descr: data.data_machine[i].descr,
                    icon: '<i class="fas fa-hdd"></i>',
                    fordelete: false,
                    areas: [],
                  };
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_machine[i].obj + '" ' +
                'data-iid="' + data.data_machine[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_machine[i].obj=='machine') {
                  html_item+='admin-crm-machine-item.php?id=';
                } 
                
                html_item+=data.data_machine[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                '<i class="fas fa-hdd"></i>' + ' ' + data.data_machine[i].descr + '</div>';
                html_item+='</div>';
  
                html_machine+=html_item;
              }
            }
            
            //////////////////////////////////////////////////////////////////
            if (this.gks_only_mobile==false) {
              html_poi='';
              for (i=0; i< data.data_poi.length; i++) {
                mykey=data.data_poi[i].obj + '_' + data.data_poi[i].id;
                if (m_points.has(mykey)==false) {
                  if (data.data_poi[i].mclass!='') {
                    mylabel={
                      text: ' ',
                      className: data.data_poi[i].mclass,
                      fontFamily: '',
                      color: "#ffffff",
                      fontSize: "12px",
                    };
                    
                  } else {
                    mylabel=data.data_poi[i].lbl;
                  }
                  myelement = new google.maps.marker.PinElement({
                    //background: "yellow",
                    scale: 1,
                    //borderColor: "#137333",
                    glyph: '',
                    //glyphColor: '',
                    
                  });  
                  
                  myelement = document.createElement('div');
                  myelement.className = 'marker_adv_div';
                  myelement.setAttribute('data-m-obj', data.data_poi[i].obj);
                  myelement.setAttribute('data-m-iid', data.data_poi[i].id);
                  myelement.innerHTML = icon_template.replace('[[1]]',(data.data_poi[i].icon=='' ? data.data_poi[i].lbl : data.data_poi[i].icon));

                  m_marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: {lat: data.data_poi[i].point.lat, lng: data.data_poi[i].point.lng},
                    title: data.data_poi[i].descr,
                    //label:mylabel,
  
                    content: myelement,
                  });
                  m_point={
                    obj: data.data_poi[i].obj,
                    id: data.data_poi[i].id,
                    type_id: data.data_poi[i].tid,
                    marker: m_marker,
                    pos: {lat: data.data_poi[i].point.lat, lng: data.data_poi[i].point.lng},
                    title: 'poi / ' + data.data_poi[i].type_descr,
                    descr: data.data_poi[i].descr,
                    icon: data.data_poi[i].icon,
                    fordelete: false,
                    areas: [],
                  };
                  
                  //$('.marker_adv_div[data-m-obj=' + data.data_poi[i].obj + '][data-m-iid=' + data.data_poi[i].id + ']').click(marker_adv_div_click);
                  
  
                  for (ps = 0; ps < data.data_poi[i].areas.polygons.length; ps++) {
                    newShapeAdd=new google.maps.Polygon({
                      type: google.maps.drawing.OverlayType.POLYGON,
                      fillColor: data.data_poi[i].areas.polygons[ps].color,
                      strokeWeight: 0,
                      fillOpacity: 0.45,
                      editable: false,
                      draggable: false,
                      map:map,
                      path:data.data_poi[i].areas.polygons[ps].points
                    });
                    m_area={
                      shape:newShapeAdd,
                      id: data.data_poi[i].id,
                      mykey: mykey,
                    };
                    m_point.areas.push(m_area);
                    
                    newShapeAdd.gks_obj=data.data_poi[i].obj;
                    newShapeAdd.gks_id=data.data_poi[i].id;
                    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
                      area_shape_click(this.gks_obj,this.gks_id);
                    }); 
                    newShapeAdd=null; 
                  }
                  for (ps = 0; ps < data.data_poi[i].areas.circles.length; ps++) {
  
                    newShapeAdd=new google.maps.Circle({
                      type: google.maps.drawing.OverlayType.CIRCLE,
                      fillColor: data.data_poi[i].areas.circles[ps].color,
                      strokeWeight: 0,
                      fillOpacity: 0.45,
                      editable: false,
                      draggable: false,
                      map:map,
                      center: new google.maps.LatLng(data.data_poi[i].areas.circles[ps].center.lat, data.data_poi[i].areas.circles[ps].center.lng),
                      radius: data.data_poi[i].areas.circles[ps].radius,
                    });
                    m_area={
                      shape:newShapeAdd,
                      id: data.data_poi[i].id,
                      mykey: mykey,
                    };
                    m_point.areas.push(m_area);
  
                    newShapeAdd.gks_obj=data.data_poi[i].obj;
                    newShapeAdd.gks_id=data.data_poi[i].id;
                    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
                      area_shape_click(this.gks_obj,this.gks_id);
                    }); 
                    newShapeAdd=null; 
                  }
                  for (ps = 0; ps < data.data_poi[i].areas.rectangles.length; ps++) {
                     newShapeAdd=new google.maps.Rectangle({
                      type: google.maps.drawing.OverlayType.RECTANGLE,
                      fillColor: data.data_poi[i].areas.rectangles[ps].color,
                      strokeWeight: 0,
                      fillOpacity: 0.45,
                      editable: false,
                      draggable: false,
                      map:map,
                      bounds: {
                        north: data.data_poi[i].areas.rectangles[ps].corner_left_top.lat,
                        south: data.data_poi[i].areas.rectangles[ps].corner_right_bottom.lat,
                        east:  data.data_poi[i].areas.rectangles[ps].corner_right_bottom.lng,
                        west:  data.data_poi[i].areas.rectangles[ps].corner_left_top.lng,
                      },
                    });
                    m_area={
                      shape:newShapeAdd,
                      id: data.data_poi[i].id,
                      mykey: mykey,
                    };
                    m_point.areas.push(m_area);
  
                    newShapeAdd.gks_obj=data.data_poi[i].obj;
                    newShapeAdd.gks_id=data.data_poi[i].id;
                    google.maps.event.addListener(newShapeAdd, 'click', function (e) {
                      area_shape_click(this.gks_obj,this.gks_id);
                    }); 
                    newShapeAdd=null; 
                  }
                  
                  m_points.set(mykey,m_point);
                  
                } else {
                  m_points.get(mykey).fordelete=false;
                }
                
                
                html_item='<div class="list_item" ' +
                'data-obj="' + data.data_poi[i].obj + '" ' +
                'data-iid="' + data.data_poi[i].id + '" ' +
                '><div class="list_itemd1">' + (i+1) + '. ' + 
                '<a href="';
                if (data.data_poi[i].obj=='poi') {
                  html_item+='admin-poi-item.php?id=';
                } 
                pr_list='';
                for (pl=0; pl<data.data_poi[i].pr.length; pl++) {
                  pr_list+='<div class="list_itemd3"><span>' +  data.data_poi[i].pr[pl].n + '</span> <b>'+  data.data_poi[i].pr[pl].c + '</b></div>';
                }
                if (pr_list=='') {
                  pr_list=gks_lang('Κενό');
                }
                
                html_item+=data.data_poi[i].id + '"><i class="enterrow fas fa-pen" title="'+gks_lang('Προβολή')+'"></i></a>' +
                data.data_poi[i].icon + ' ' + data.data_poi[i].descr + '</div>';
                if (pr_list!='') {
                   html_item+='<div class="list_itemd2';
                   if (data.data_poi[i].pr.length>1) html_item+=' list_itemdmore';
                   else if (data.data_poi[i].pr.length==0) html_item+=' list_itemdempty';
                   html_item+='">' + pr_list + '</div>';
                }
                html_item+='</div>';
                
                
                html_poi+=html_item;
              }
            }
            
            
            if (this.gks_only_mobile==false) {
              for ([key, value] of m_points) {
                if (m_points.get(key).fordelete)  {
                  m_points.get(key).marker.setMap(null);
                  m_points.get(key).marker=null;
                  for (ps=0; ps<m_points.get(key).areas.length;ps++) {
                    m_points.get(key).areas[ps].shape.setMap(null);
                    m_points.get(key).areas[ps].shape=null;
                  }
                  m_points.get(key).areas=[];
                  if (m_points.get(key).paths !== undefined) {
                    for ([keypp, valuepp] of m_points.get(key).paths) {
                      m_points.get(key).paths.get(keypp).gpath.setMap(null);
                      m_points.get(key).paths.get(keypp).gpath=null;
                      m_points.get(key).paths.delete(keypp);
                    }
                  }
                  
                  m_points.delete(key);
                }
              }
              
              
            
            
            
              $('#list_users').html(html_users);
              $('#list_calendar').html(html_calendar);
              $('#list_lead').html(html_lead);
              $('#list_task').html(html_task);
              $('#list_machine').html(html_machine);
              $('#list_poi').html(html_poi);
              $('#list_users .list_item, #list_lead .list_item, #list_calendar .list_item, #list_task .list_item, #list_machine .list_item, #list_poi .list_item').click(list_item_click);
              
              if (last_selected_key[1]>0) {
                list_item_click_run(last_selected_key[0],last_selected_key[1]);
              }
              
              window.setTimeout(() => {
                $('.marker_adv_div').off().click(marker_adv_div_click);
              }, 1000);
            }
            
          
            //console.log(m_points.size);
            
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
        
        mygetdata_xhr_running=false;
      }
      
    });     
    
  }
  var last_selected_key=['',0,''];
  
  function list_item_click() {

    if ($(this).hasClass('list_item_selected')) {
      $('.list_item_selected').each(function() {
        exist_obj=$(this).attr('data-obj');
        exist_iid=$(this).attr('data-iid');
        exist_mykey=exist_obj + '_' + exist_iid;
        //console.log(exist_mykey);
        for(ps=0; ps < m_points.get(exist_mykey).areas.length; ps++) {
          m_points.get(exist_mykey).areas[ps].shape.setOptions({fillOpacity: 0.45});
        }
      });
      
      $('.list_item_selected').removeClass('list_item_selected');
      $('.marker_adv_div_selected').removeClass('marker_adv_div_selected');      
      infoWindow_userpos.close();
      last_selected_key=['',0,''];
      
      return  
    }
        
    obj=$(this).attr('data-obj');
    iid=$(this).attr('data-iid');
    list_item_click_run(obj,iid);
  }
  function marker_adv_div_click() {
    event.stopPropagation();
    obj=$(this).attr('data-m-obj');
    iid=$(this).attr('data-m-iid');
    list_item_click_run(obj,iid);
   
    panel_obj=obj;
    if (obj=='poi') panel_obj='poi';
   
    if (panel_tool_selected!=panel_obj) {
      panel_tool_selected=panel_obj;
      gks_panel_tool_change();
    }
  
  }
  
  function area_shape_click(obj,iid) {
    //console.log(obj,iid);
    list_item_click_run(obj,iid);
  }
  
  function list_item_click_run(obj,iid) {
    
    
    
    $('.list_item_selected').each(function() {
      exist_obj=$(this).attr('data-obj');
      exist_iid=$(this).attr('data-iid');
      exist_mykey=exist_obj + '_' + exist_iid;
      //console.log(exist_mykey);
      for(ps=0; ps < m_points.get(exist_mykey).areas.length; ps++) {
        m_points.get(exist_mykey).areas[ps].shape.setOptions({fillOpacity: 0.45});
      }
    });
    
    $('.list_item_selected').removeClass('list_item_selected');
    $('.marker_adv_div_selected').removeClass('marker_adv_div_selected');
    
    mykey=obj + '_' + iid;
    last_selected_key=[obj,iid,mykey];
    if (m_points.has(mykey)==false) {
      last_selected_key=['',0,''];
      return;
    }
    $('.list_item[data-obj=' + obj + '][data-iid=' + iid + ']').addClass('list_item_selected');  
    $('.marker_adv_div[data-m-obj=' + obj + '][data-m-iid=' + iid + ']').addClass('marker_adv_div_selected');
    
    fff=$('.list_item[data-obj=' + obj + '][data-iid=' + iid + ']');
    if (fff.length>0) {
      //document.getElementById('gggggg').scrollIntoView({block: "nearest",inline : 'nearest'});
      fff[0].scrollIntoView({block: "nearest",inline : 'nearest'});
    }
    
    for(ps=0; ps < m_points.get(mykey).areas.length; ps++) {
      m_points.get(mykey).areas[ps].shape.setOptions({fillOpacity: 0.9});
    }
          
    

    //infoWindow_userpos.setPosition(m_points.get(mykey).pos);
    infoWindow_userpos.setHeaderContent(m_points.get(mykey).title);
    infoWindow_userpos.setContent(m_points.get(mykey).icon + ' ' + m_points.get(mykey).descr);
    infoWindow_userpos.open({map:map,anchor: m_points.get(mykey).marker});
    
    
  }

  // ---------------------    

  
  window.showmap_run = function() {
  //function showmap_run() {
    
    
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
            //markerpos.setOptions({position: myLatLng});
            markerpos.position=myLatLng;
            map.setOptions({center: myLatLng});
            map.setOptions({zoom: 17});
  
            jQuery('#poi_map_latitude').val(place_map_latitude);
            jQuery('#poi_map_longitude').val(place_map_longitude);
  
        }, function() {
          map.setOptions({zoom: 17});
          handleLocationError(true, infoWindow_userpos, map.getCenter());
        });
      } else {
        // Browser doesn't support Geolocation
        map.setOptions({zoom: 17});
        handleLocationError(false, infoWindow_userpos, map.getCenter());
      }
    }      
  
    myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
  
    initMap();
    markerpos.addListener('drag', handleEvent_Marker);
    markerpos.addListener('dragend', handleEvent_Marker);
  
    text_search_autocomplete('text_search','poi_map_latitude','poi_map_longitude');
    
    map.addListener('bounds_changed', () => {
      // 3 seconds after the center of the map has changed, pan back to the
      // marker.
      //console.log('bounds_changed');
      bounds=map.getBounds();
      mybounds={
            north: bounds.getNorthEast().lat(),
            south: bounds.getSouthWest().lat(), 
            east:  bounds.getNorthEast().lng(),
            west:  bounds.getSouthWest().lng()
      }
      
      
      
      clearTimeout(mygetdata_timer);
      mygetdata_timer = window.setTimeout(() => {
        mygetdata(mybounds,false);
      }, 1000);
    });
      
  
    map_is_open=true;
  }
  
  function handleEvent_Marker(event) {
    place_map_latitude=event.latLng.lat();
    place_map_longitude=event.latLng.lng();
    document.getElementById('poi_map_latitude').value = place_map_latitude;
    document.getElementById('poi_map_longitude').value = place_map_longitude;
    set_hash();
  }
  
  
  
  function initMap() {
    
    mcenter=myLatLng;
    if (mydata.mlat!=0 && mydata.mlng!=0) {
      mcenter={lat: mydata.mlat, lng: mydata.mlng};
    }
    
    map = new google.maps.Map(
      document.getElementById('gks_map_div_panel_map_show'), 
      {
        center: mcenter,
        zoom: mydata.zoom,
        //gestureHandling: 'greedy'
        mapId: "gks1234567890",
        
      }
    );
    
    infoWindow_userpos = new google.maps.InfoWindow({map: map});
    
      
    measureTool = new MeasureTool(map, {
      contextMenu: false,
      unit: MeasureTool.UnitTypeId.METRIC //IMPERIAL
    });
        
    
    const marker_blue = document.createElement("img");
    marker_blue.src ="/my/img/markers/marker_blue.png";              
    markerpos = new google.maps.marker.AdvancedMarkerElement({
      map: map,
      position: myLatLng,
      title: gks_lang('Σημείο'),
      gmpDraggable: true,
      //content: myelement.element,
      content:marker_blue,
    });
    
  }

  function text_search_autocomplete(ma_odos,ma_latitude,ma_longitude) {
    
    //console.log('text_search_autocomplete',document.readyState);
    if (document.readyState == "complete") {
      gks_window_load_map_autocomplete(ma_odos,ma_latitude,ma_longitude);
    } else {
      window.addEventListener('load', function() {
        gks_window_load_map_autocomplete(ma_odos,ma_latitude,ma_longitude);
      });
    }    
  }
 
  function gks_window_load_map_autocomplete(ma_odos,ma_latitude,ma_longitude) {
    //console.log('gks_window_load_map_autocomplete fire',ma_odos,ma_latitude,ma_longitude);
    //google.maps.event.addDomListener(window, 'load', function() {
    
    mydiv1=document.createElement('div');
    mydiv1.setAttribute('id', ma_odos + '_gmac1');
    mydiv1.setAttribute('class', 'gks_google_autocomplete_div1');
    mydiv1.setAttribute('data-ma_latitude', ma_latitude);
    mydiv1.setAttribute('data-ma_longitude',ma_longitude);
    mydiv1.setAttribute('data-admincrmmap','1');
  
    mydiv2=document.createElement('div');
    mydiv2.setAttribute('id', ma_odos + '_gmac2');
    mydiv2.setAttribute('class', 'gks_google_autocomplete_div2');
    mydiv1.appendChild(mydiv2);
  
    inputElement =document.getElementById(ma_odos);
    inputElement.addEventListener('input', gks_gmac_makeAutocompleteRequest);
    inputElement.after(mydiv1);  
    inputElement.setAttribute('placeholder', gks_lang('Αναζήτηση'));
        
    return;
    /*
    var autocomplete_googlemaps = new google.maps.places.Autocomplete(document.getElementById(ma_odos));
    google.maps.event.addListener(autocomplete_googlemaps, 'place_changed', function () {
      var myplace = autocomplete_googlemaps.getPlace();
      //console.log(myplace);
      my_formatted_address=myplace.formatted_address;
      if (myplace.name !== undefined) my_formatted_address+='<br>'+gks_lang('Όνομα')+': ' + myplace.name;
      if (myplace.international_phone_number !== undefined) my_formatted_address+='<br>'+gks_lang('Τηλέφωνο')+': ' + myplace.international_phone_number;
      if (myplace.website !== undefined) my_formatted_address+='<br>'+gks_lang('Ιστότοπος')+': <a href="' + myplace.website + '" target="_blank">' + myplace.website + '</a>';
      if (myplace.url !== undefined) my_formatted_address+='<br>Google Map: <a href="' + myplace.url + '" target="_blank">' + myplace.url.substring(0, 30) + (myplace.url.length>30 ? '...' : '') + '</a>';
      $('#' + ma_odos + '_auto_googlemaps small').html(my_formatted_address);
      $('#' + ma_odos + '_auto_googlemaps').slideDown();
      
      if (ma_latitude!='' && ma_longitude!='') {
        $('#' + ma_latitude).val(myplace.geometry.location.lat());
        $('#' + ma_longitude).val(myplace.geometry.location.lng());
      }
      
      place_map_latitude=myplace.geometry.location.lat();
      place_map_longitude=myplace.geometry.location.lng();
      
      myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
      if (typeof markerpos != 'undefined') markerpos.setOptions({position: myLatLng});
      if (typeof markerpos != 'undefined') map.setOptions({center: myLatLng});
      map.setOptions({zoom: 17});
      
    }); 
    */       
    
  }
  
  
  
  var panel_tool_list=['users','appmobile','lead','calendar','task','machine','poi'];

  function gks_panel_tool_change() {
    panel_tool_list.forEach((sss) => {
      if (sss==panel_tool_selected) {
        $('#gks_map_div_panel_' + sss).show();
        $('.button_tool_settings[data-id=' + sss + ']').removeClass('btn-success').addClass('btn-warning');
      } else {
        $('#gks_map_div_panel_' + sss).hide();
        $('.button_tool_settings[data-id=' + sss + ']').addClass('btn-success').removeClass('btn-warning');
      }
    });
    set_hash();
  }
  $('.button_tool_settings').click(function() {
    panel_tool_selected=$(this).attr('data-id');  
    gks_panel_tool_change();  
  });
  

  
  $('#users_enable, #appmobile_enable, #lead_enable, #calendar_enable, #task_enable, #machine_enable, #poi_enable').change(function() {
    if ($('#appmobile_enable').is(':checked')==false) {
      $('#list_appmobile').html('');  
      appmobile_last_id_gps = new Map();
    }

    mygetdata(gks_bounds_local,false);

  });

  
  
  gks_panel_tool_change();
  
  
  
  var my_timer_appmobile = window.setInterval(() => {
    if (mygetdata_xhr_running) return;
    if ($('#appmobile_enable').is(':checked')==false) return;
    //console.log('my_timer_appmobile');
    mygetdata(gks_bounds_local,true);
  }, 2000);
  
  var prev_path_selected=['',0,'',''];
  function list_path3_click() {
    event.stopPropagation();
    pp=$(this).attr('data-dname');
    iid=parseInt($(this).parent().parent().attr('data-iid'));
    if (isNaN(iid)) iid=0;
    if (iid==0) return;
    //console.log(pp,iid);
    if ($(this).hasClass('list_path3_selected')) {
      $(this).removeClass('list_path3_selected');
      if (prev_path_selected[1]>0) {
        ggg_prev=m_points.get(prev_path_selected[2]).paths.get(prev_path_selected[3])
        ggg_prev.gpath.setOptions({strokeColor: '#FF0000','strokeWeight': 2});
        m_points.get(prev_path_selected[2]).paths.set(prev_path_selected[3],ggg_prev);
      }
      prev_path_selected=['',0,'',''];
      return;  
    }
    
    $('.list_path3_selected').removeClass('list_path3_selected');
    $(this).addClass('list_path3_selected');
    
    mykey='appmobile_' + iid;
    
    ggg=m_points.get(mykey).paths.get(pp)
    ggg.gpath.setOptions({strokeColor: '#0000FF','strokeWeight': 3});
    m_points.get(mykey).paths.set(pp,ggg);
    
    if (prev_path_selected[1]>0) {
      ggg_prev=m_points.get(prev_path_selected[2]).paths.get(prev_path_selected[3])
      ggg_prev.gpath.setOptions({strokeColor: '#FF0000','strokeWeight': 2});
      m_points.get(prev_path_selected[2]).paths.set(prev_path_selected[3],ggg_prev);
    }
    prev_path_selected=['appmobile',iid,mykey,pp];
    
    var bounds_path = new google.maps.LatLngBounds();
    ggg.gpath.getPath().forEach(function(item, index) {
        bounds_path.extend(new google.maps.LatLng(item.lat(), item.lng()));
    });
    map.fitBounds(bounds_path);
    
  }
  
  var mygetdata_timer=null;


  gks_page_loading=false;

     
});


function mapDateDiff(difference) {
      
  //Arrange the difference of date in days, hours, minutes, and seconds format
  let days = Math.floor(difference / (1000 * 60 * 60 * 24));
  let hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
  let seconds = Math.floor((difference % (1000 * 60)) / 1000);
  
  aret=gks_lang('Συνολικός χρόνος')+': '+days+' '+gks_lang('ημέρες')+' '+hours+' '+gks_lang('ώρες')+' '+minutes+' '+gks_lang('λεπτά')+' '+seconds+' '+gks_lang('δευτερόλεπτα');
  anum=days+'.'+
    (hours<=9 ?   '0'+ hours : hours)+':'+
    (minutes<=9 ? '0' + minutes : minutes)+':'+
    (seconds<=9 ? '0' + seconds : seconds);
    
  return {'s':aret,'n': anum}
}

