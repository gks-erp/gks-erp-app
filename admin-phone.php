<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Τηλέφωνο');
$nav_active_array=array('crm','manage_phones','manage_phone');



db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_voip_calls','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_voip_params=gks_voip_user_params();

$sql="SELECT id_erp_app,erp_app_name
FROM gks_erp_app
WHERE erp_app_disabled=0
and voip_ip<>'' and voip_AIM_port>0 and voip_AIM_username<>'' and voip_AIM_password<>''
and erp_app_last_ping>date_sub(now(),interval 15 minute)
and voip_call_monitoring=1";
$result = $db_link->query($sql);   
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $ret['error']='sql error';return $ret;}

$voip_call_monitoring=$result->num_rows>0;


include_once('_my_header_admin.php');
?>
<style>
.voip_more_data_row {
  color:#0069d9;
  cursor:pointer;
  font-size: 20px;
}  
.p-40 {
  padding-right:30px !important;  
}

#gks_calls_table th {
  border-top:1px solid black;
}
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-4">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Νέα κλήση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('call');?>> 
          <div class="form-group row">
            <label for="voipaimoriginatecall_extension" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερικό');?>:</label>
            <div class="col-md-8">
              <input id="voipaimoriginatecall_extension" type="text" class="form-control form-control-sm" value="" autocomplete="off" placeholder="<?php echo gks_lang('π.χ.');?> 111"/>
            </div>
          </div>
          <div class="form-group row">
            <label for="voipaimoriginatecall_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="voipaimoriginatecall_phone" type="text" class="form-control form-control-sm" value="" autocomplete="off" placeholder="<?php echo gks_lang('π.χ.');?> 6971881406"/>
            </div>
          </div>
          <div style="text-align:center;">
            <button type="button" class="btn btn-primary" id="mystartcall"><?php echo gks_lang('Έναρξη κλήσης');?></button>
          </div>
          <div>
            <span class="form-control-plaintext form-control-sm" style="height:unset;padding:0px;border:0px solid black;" id="run_command_voipaimoriginatecall_result"></span>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ρυθμίσεις για νέα κλήση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('call');?>> 
          <div class="form-group row">
            <label for="playsound" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναπαραγωγή ήχου');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="playsound" value="1" class="switchery1_sel">
              <small class="form-text text-muted"><?php echo gks_lang('Όταν θα έρθει η εισερχόμενη κλήση εάν θα αναπαραχθεί ο ήχος');?>: <i class="fas fa-volume-up" id="playsound_icon" style="color:blue;font-size:150%;cursor:pointer;opacity:0.4;"></i></small>
              <small class="form-text text-muted"><?php echo gks_lang('Για να μπορεί λειτουργήσει θα πρέπει πρώτα να κάνετε ένα κλικ σε οποιαδήποτε σημείο της σελίδας σελίδα');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="openwindow" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Άνοιγμα παραθύρου');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="openwindow" value="1" class="switchery1_sel">
              <small class="form-text text-muted"><?php echo gks_lang('Όταν θα έρθει η εισερχόμενη κλήση εάν θα ανοίγει σε άλλο παράθυρο η επισκόπηση της επαφής');?></small>
              <small class="form-text text-muted"><?php echo gks_lang('Για να λειτουργήσει θα πρέπει να είναι εγκατεστημένη η εφαρμογή gks ERP App Desktop σε αυτόν τον Η/Υ');?></small>
              <small class="form-text text-muted"><?php echo gks_lang('Πληκτρολογήστε παρακάτω την TCP Port που ακούει η εφαρμογή gks ERP App Desktop');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="openwindow_port" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('TCP Port');?>:</label>
            <div class="col-md-8">
              <input type="number" id="openwindow_port" value="" class="form-control form-control-sm" placeholder="<?php echo gks_lang('π.χ.');?> 55555"></td>  
            </div>
          </div>  
        </div>
      </div>      
    </div>
    
    <div class="col-md-4">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφές Κλήσεων');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('log');?>> 
          <div style="text-align:center;margin-bottom:10px;">
            <button type="button" class="btn btn-primary" id="getlistcalls_run"><?php echo gks_lang('Ανανέωση');?></button>
          </div>
          <div style="width:calc(100% - 0px);height:10px;background-color:lightblue;margin:0px 0px 0px 0px">
            <div id="psososto_refresh" style="width:100%;height:10px;background-color:darkblue;"></div>
          </div>
          <div id="getlistcalls"></div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αγαπημένα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('fav');?>> 
<?php
          $sql="select * from gks_voip_favorites
          where user_id=".$my_wp_user_id."
          order by mysortorder,id_voip_favorite";
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="favorites_table" style="width:100%;">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo gks_lang('Τηλέφωνο');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo gks_lang('Όνομα');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><span class="tooltipster" title="<?php echo gks_lang('Σειρά Ταξινόμησης');?>"><?php echo gks_lang('ΣειράΤ');?></span></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="favorites_tr_exist" data-id="<?php echo $row_list['id_voip_favorite'];?>">
              <th scope="row" nowrap align="center" class="favorites_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_favorites_delete_after|<?php echo $row_list['id_voip_favorite'];?>" data-id="<?php echo $row_list['id_voip_favorite'];?>" data-model="gks_voip_favorites">            
              </td>
              <td class="p-40">
                <a href="tel:<?php echo $row_list['phone'];?>" class="<?php echo $gks_voip_params['class_span'];?>"><?php echo $row_list['phone'];?></a>
                <?php echo $gks_voip_params['html_after_span'];?>
              </td>
              <td><?php echo $row_list['nickname'];?></td>
              <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row_list['mysortorder'];?>">
                <i class="fas fa-arrows-alt-v"></i>
                <span><?php echo $row_list['mysortorder'];?></span>
              </td>              
            </tr>
          <?php } ?>

          </tbody>
          <tfoot>
            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;"><i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i></td>
              <td nowrap><input type="text" name="favorites_phone"    id="favorites_phone"    class="form-control form-control-sm" placeholder="<?php echo gks_lang('π.χ.');?> 6971881406"></td>  
              <td nowrap><input type="text" name="favorites_nickname" id="favorites_nickname" class="form-control form-control-sm" placeholder="<?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Κώστας');?>"></td>  
              <td nowrap></td>
            </tr>
            <tr>
              <td colspan="5" style="text-align:center;">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm1 btn-primary" id="add_favorites"><?php echo gks_lang('Προσθήκη');?></button>
              </td>
            </tr>
          </tfoot>
          </table> 
        </div>
      </div>
    </div>    
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<audio id="voip_call_audio" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/my/audio/ring.mp3" type="audio/mpeg"></audio>
  
<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  voip_extension_def=gks_getCookie('voip_extension_def');
  if (voip_extension_def==null || voip_extension_def=='') {
    voip_extension_def='';
    if (from_php_gks_voip_params.extensions.length==1) voip_extension_def=from_php_gks_voip_params.extensions[0];
  }
  //console.log(voip_extension_def);
  $('#voipaimoriginatecall_extension').val(voip_extension_def);
 
  $('#mystartcall').click(function() {
    
    if (from_php_gks_voip_params.id_erp_app<=0) {
      myalert('error:'+gks_lang('Δεν βρέθηκε εφαρμογή gks ERP App Desktop ενεργή και κατάλληλα ρυθμισμένη'));
      return;
    }
    if ($('#voipaimoriginatecall_extension').val().trim()=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το εσωτερικό σας αριθμό'));
      return;
    }
    if ($('#voipaimoriginatecall_phone').val().trim()=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε το τηλέφωνο που θα γίνει η κλήση'));
      return;
    }

    $('#run_command_voipaimoriginatecall_result').html('');

    
    datasend='id=' + from_php_gks_voip_params.id_erp_app;
    datasend+='&cmd='+encodeURIComponent($.base64.encode('run_command_voipaimoriginatecall'));
    datasend+='&extension=' + encodeURIComponent($.base64.encode($('#voipaimoriginatecall_extension').val().trim())); 
    datasend+='&phone=' + encodeURIComponent($.base64.encode($('#voipaimoriginatecall_phone').val().trim())); 
     
    
    $.ajax({
			url: '/my/admin-erp-app-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#run_command_voipaimoriginatecall_result').html($.base64.decode(data.html));
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});    
  });

  $('#add_favorites').click(function(event) { 
     
    datasend='cmd=addfavorites';
    datasend+='&phone='   +encodeURIComponent($.base64.encode($('#favorites_phone').val()));
    datasend+='&nickname='+encodeURIComponent($.base64.encode($('#favorites_nickname').val()));
    
    //$('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-phone-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_last=$('#favorites_table tbody tr:last');
            if (tr_last.length>=1) {
              tr_last.after(row_html);
            } else {
              $('#favorites_table tbody').html(row_html);
            }
            
            $('.favorites_tr_new .deleterow').click(deleterow_click); 
            $('.favorites_tr_new .gks_voip_originate_after_span').click(gks_voip_originate_click);
  
            $('.favorites_tr_new').each(function() {
              $(this).removeClass('favorites_tr_new').addClass('favorites_tr_exist');
            });
            var favorites_aa=0;
            $('#favorites_table .favorites_aa').each(function () {
              favorites_aa++;
              $(this).html(favorites_aa);  
            });

            $('#favorites_phone').val('');
            $('#favorites_nickname').val('');	
            $('#favorites_table > tbody').sortable('refresh');	
            			  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }); 
  
  window.gks_fnc_favorites_delete_after = function (myargs) {
    
    $("body").removeClass("myloading");
    $('.favorites_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var favorites_aa=0;
      $('#favorites_table .favorites_aa').each(function () {
        favorites_aa++;
        $(this).html(favorites_aa);  
      });    
    });
  }

  $('#favorites_phone, #favorites_nickname').autocomplete({
    source: function(request, response) {
      
      mydata={
        term: request.term,
        comm_type:'phone',
        //mobile: 1,
      };
      $.ajax({
        url: 'admin-autocomplete-comm.php',
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
      
      setTimeout(function(a,b) {
        $('#favorites_phone').val(a);
        $('#favorites_nickname').val(b);
      },300,ui.item.value,ui.item.user);
    },
    change: function (event, ui) {
      
        //if(!ui.item){
        //  $('#favorites_phone').attr('data-user_id','0');
        //}
    },
    create: function(event, ui){
      
      $(this).attr('autocomplete',autocomplete_gks_disable);
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + (item.descr + ' ' + item.user).trim() + '</span>')
          .appendTo(ul);
      };
    },
    
  });
  
    
  $('#favorites_table > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_voip_favorites',mylist,'#favorites_table > tbody');
      var favorites_aa=0;
      $('#favorites_table .favorites_aa').each(function () {
        favorites_aa++;
        $(this).html(favorites_aa);  
      });       
    }
  });
  
  var latest_id_voip_call=-1;
  function gks_get_list_calls() {
    time_start = performance.now();
    datasend='cmd=getlistcalls';
    $.ajax({
			url: 'admin-phone-cmd.php',
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
  					$('#getlistcalls').html($.base64.decode(data.html));
            $('#getlistcalls .gks_voip_originate_after_span').click(gks_voip_originate_click);	
            latest_id_voip_call=data.latest_id_voip_call;
            //console.log(latest_id_voip_call);	
            $('.voip_more_data_row').click(voip_more_data_row_click);  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
		
   
  }
  gks_get_list_calls();
  $('#getlistcalls_run').click(function() {
    gks_get_list_calls();
  });

  var timer_refresh  = setInterval(myTimer, 100);
  var time_start = performance.now();
  var time_end = 2*60*1000; //2 lepta
  function myTimer() {
    var time_now = performance.now();
    diafora = (time_now - time_start);
    //console.log(diafora);
    
    pososto = diafora/time_end;
    pososto=(100-pososto*100);
    
    if (pososto<0) {
      pososto=0;
      gks_get_list_calls();
    }
    //console.log(pososto);
    $('#psososto_refresh').css('width',pososto.formatMoney(2,'.','') + '%');
    
  }
  opennewwindowobj=[];
  
  function gks_get_latestphone_call() {
    <?php if ($voip_call_monitoring==false) echo 'return;';?>
    
    //console.log('try gks_get_latestphone_call',latest_id_voip_call);
    if (latest_id_voip_call==-1) {
      setTimeout(gks_get_latestphone_call,500);
      return; 
    }
    datasend='cmd=getlatestphonecalls&latest_id_voip_call='+latest_id_voip_call;
    $.ajax({
			url: 'admin-phone-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				setTimeout(gks_get_latestphone_call,500);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					//console.log(data);
  				  latest_id_voip_call=data.latest_id_voip_call;
  				  if (data.data.length>0) {
  				    gks_get_list_calls();
  				    
  				    mytext=gks_lang('Κλήση')+'<br>';
  				    for(i=0; i<data.data.length;i++) {
  				      mytext+=data.data[i].src;
  				      if (data.data[i].gks_user_id>0) {
  				        mytext+='<br><a href="admin-users-item-overview.php?id='+data.data[i].gks_user_id+'" target="_blank">'+data.data[i].gks_nickname+'</a>';
  				      }
  				      mytext+='<br>';
  				      if (data.data[i].gks_user_id>0) {
  				        uuu=window.location.origin+'/my/admin-users-item-overview.php?id='+ data.data[i].gks_user_id;
  				        if (option_openwindow && option_openwindow_port>0) {
  				          setTimeout(function(uuu) {
  				            myurl_get='http://localhost:'+option_openwindow_port+'/openurl';
  				            datasend='url='+encodeURIComponent($.base64.encode(uuu));
                      $.ajax({
                    		url: myurl_get,
                    		type: 'POST',
                    		cache: false,
                    		dataType: 'json',
                    		data: datasend,
                    		error : function(jqXHR ,textStatus,  errorThrown) {
                    			console.log('error openurl',textStatus,jqXHR.responseText);
                    		},
                    		success: function(data) {
                    			console.log('ok openurl',data);
                    		}
                    	});
                    }, 300, uuu);
  				        }
  				      }
  				      if (option_playsound) {
				          document.querySelector('#voip_call_audio').play();
				        }
  				    }
  				    
              window.clearTimeout(div_info_card_timer);
              $('#div_info_down').html(gks_lang('σε [1]').replaceAll('[1]',5));
              div_info_card_timer = setInterval(div_info_card_myTimer, 500);
              div_info_card_time_start = performance.now();
              div_info_card_time_end = 15*1000; //15 secs
              $('#div_info_card_text').html(mytext);
  					  $('#div_info_card').show();
    					    				    
  				  }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				setTimeout(gks_get_latestphone_call,500);
			}
			
		});    
  }
  
  setTimeout(gks_get_latestphone_call,500);


  var div_info_card_timer;
  var div_info_card_time_start;
  var div_info_card_time_end;


  function div_info_card_myTimer() {
    var div_info_card_time_now = performance.now();
    diafora = (div_info_card_time_now - div_info_card_time_start);
    diafora=div_info_card_time_end-diafora;
    //console.log(diafora);
    
    down_secs = Math.round(diafora/1000);
    $('#div_info_down').html(gks_lang('σε [1]').replaceAll('[1]',down_secs));
    
    if (down_secs<0) {
      $('#div_info_card').hide();
      window.clearTimeout(div_info_card_timer);
    }
  }
  

  $('body').append( `<div id="div_info_card" style="
  background-color: rgba(230,250,230,0.9);
  border: 4px solid #222222;
  position: absolute;left: 20px;top: 50px;right: 20px;bottom: 50px;
  border-radius: 100px;
  box-shadow: 0 14px 18px 0 rgba(0, 0, 0, 0.5), 0 16px 50px 0 rgba(0, 0, 0, 0.9);
  z-index:3;
  display:none;">
  <table style="height:100%;width:100%;">
    <tr>
     <td style="text-align: center;font-size:500%" id="div_info_card_td">
      <div id="div_info_card_text" style="font-weight: bold;"></div> 
      <div style="padding-top: 20px;">
        <button type="submit" id="div_info_card_button" style="
          font-size: 30%;
          border-radius: 50px !important;
          background: #73AD21 !important;
          cursor: pointer !important;
          border: 1px solid #40630e !important;
          margin: 0px 5px 0px 5px !important;
          padding: 10px 15px 10px 15px !important;
          color: #ffffff !important;"><?php echo gks_lang('Απόκρυψη');?> <span id="div_info_down"></span></button>
      </div> 
      
     </td>  
    </tr>  
  </table>
</div>`);

    
  $('#div_info_card_button').click(function() {
    $('#div_info_card').hide();  
    window.clearTimeout(div_info_card_timer);
  });
  $('#div_info_card').click(function() {
    window.clearTimeout(div_info_card_timer);
    $('#div_info_down').html('');
  });
    

  
  var option_playsound=true;
  vvv=gks_getCookie('voip_new_call_playsound');
  if (vvv!=null) option_playsound=vvv=='1';
  if (option_playsound) {
    $('#playsound').click();
  }
  $('#playsound').change(function() {
    gks_setCookie('voip_new_call_playsound',($('#playsound').is(':checked')?'1':'0'),365*24*60*60,'/my/admin-phone.php');
    option_playsound=$('#playsound').is(':checked');
  });
  
  var option_openwindow=false;
  vvv=gks_getCookie('voip_new_call_openwindow');
  if (vvv!=null) option_openwindow=vvv=='1';
  if (option_openwindow) {
    $('#openwindow').click();
  }
  $('#openwindow').change(function() {
    gks_setCookie('voip_new_call_openwindow',($('#openwindow').is(':checked')?'1':'0'),365*24*60*60,'/my/admin-phone.php');
    option_openwindow=$('#openwindow').is(':checked');
  });
 
  var option_openwindow_port=0;
  vvv=gks_getCookie('voip_new_call_openwindow_port');
  if (vvv!=null) option_openwindow_port=parseInt(vvv);if (isNaN(option_openwindow_port)) option_openwindow_port=0;
  if (option_openwindow_port>0) $('#openwindow_port').val(option_openwindow_port);
  $('#openwindow_port').change(function() {
    option_openwindow_port=parseInt($('#openwindow_port').val());
    if (isNaN(option_openwindow_port)) option_openwindow_port=0;
    gks_setCookie('voip_new_call_openwindow_port',option_openwindow_port,365*24*60*60,'/my/admin-phone.php');
  }); 



  
  function voip_more_data_row_click() {
    uniqueid=$(this).attr('data-uniqueid').trim();
    if (uniqueid=='') return;
    
    data_status=$(this).attr('data-status');
    if (data_status=='1') {
      $('.gks_tr2_uniqueid[data-tr2-uniqueid="' + uniqueid + '"]').remove();
      $(this).attr('data-status','0');
      $(this).addClass('fa-caret-square-down').removeClass('fa-caret-square-up');
    } else {
      $(this).addClass('fa-caret-square-up').removeClass('fa-caret-square-down');
      $(this).attr('data-status','2');
      datasend='cmd=percall';
      datasend+='&uniqueid='+uniqueid;
      datasend+='&view=short';
      $.ajax({
    		url: 'admin-phone-cmd.php',
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
    					//console.log(data.uniqueid);
    					//console.log(data.html);
    					elemtr=$('.gks_tr1_uniqueid[data-tr1-uniqueid="' + data.uniqueid + '"]');
    					if (elemtr.length!=1) return;
    					htmltr='<tr class="gks_tr2_uniqueid" data-tr2-uniqueid="' + data.uniqueid + '">'+
    					'<td colspan="5">'+
    					data.html+
    					'</td></tr>';
    					elemtr.after(htmltr);
    					$('.voip_more_data_row[data-uniqueid="' + data.uniqueid + '"]').attr('data-status','1');
    					  
    					//$('.gks_tr2_uniqueid[data-tr2-uniqueid="'+ data.uniqueid + '"] .mydivexpand').click(gks_mydivexpand_click);
    					
    				} else {
    					myalert('error:' + $.base64.decode(data.message));
    				}
    			}
    		}
    		
    	});
    }
  }
  
  $('body').click(function() {
    $('#playsound_icon').css('opacity','unset');
  });

  $('#playsound_icon').click(function() {
    document.querySelector('#voip_call_audio').play();
  });
     
});

</script>


<?php
include_once('_my_footer_admin.php');


