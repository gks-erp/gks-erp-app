<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Σάρωση QR Code');
$nav_active_array=array('accounting','accounting_qrcode');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings_users','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

//print '<pre>';print_r($gks_user_settings);die();

$user_companys=gks_get_companys_list();
$def_company_qrcode='0|0'; if (isset($gks_user_settings['gks_acc_inv']['def_company_qrcode'])) $def_company_qrcode=$gks_user_settings['gks_acc_inv']['def_company_qrcode'];
//print '<pre>';print_r($def_company_qrcode);die();


$GKS_ERP_APP_MOBILE_VER=gks_erp_app_mobile_get_later_version();
$GKS_ERP_APP_MOBILE_VER_link='https://tools.gks.gr/download/gks_ERP_App_Mobile_v'.$GKS_ERP_APP_MOBILE_VER.'.apk';
$GKS_ERP_APP_MOBILE_VER_img='';
include_once('vendor_inc/phpqrcode/qrlib.php');
$fileName = 'qr_code_temp_'.md5($GKS_ERP_APP_MOBILE_VER_link).'.png';
$pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
$urlRelativeFilePath = GKS_SITE_URL.'my/temp/'.$fileName;
if (!file_exists($pngAbsoluteFilePath)) {
  QRcode::png($GKS_ERP_APP_MOBILE_VER_link, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 0);
} 
if (file_exists($pngAbsoluteFilePath)) {
  $GKS_ERP_APP_MOBILE_VER_img=$urlRelativeFilePath;
} else {
	$GKS_ERP_APP_MOBILE_VER_img='/my/img/gks_ERP_App_Mobile_qrcode.png';
}

include_once('_my_header_admin.php');
?>
<link href="css/scan.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<script src="/my/js/html5-qrcode.min.js"></script>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σάρωση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('scan');?>> 

			    <div id="qr-reader" style="width:100%"></div>
			    <div id="qr-reader-results"></div>
            	
        </div>
      </div>
      

            
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σαρώσεις από το gks ERP App Mobile');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('scans');?>> 
        	<div id="scan_list">
        		<div id="gks_item_list">
					<?php
					$last_recid=0;
					$myaa=0;
					$sql="select * from gks_qrcode_scan where user_id_edit in (0,2,".$my_wp_user_id.") order by id_qrcode_scan desc";
					$result = $db_link->query($sql);  
					if (!$result) {debug_mail(false,'error sql',$sql); echo 'sql error';die();}
					while ($row = $result->fetch_assoc()) {
						$row['id_qrcode_scan']=intval($row['id_qrcode_scan']);
						if ($last_recid<$row['id_qrcode_scan']) $last_recid=$row['id_qrcode_scan'];
						$mytext=trim_gks($row['mytext']);
						$mycmdhtml='';
						if (startwith($mytext,'https://www1.aade.gr/tameiakes/myweb/q1.php') or 
						    startwith($mytext,'https://www1.gsis.gr/tameiakes/myweb/q1.php')) {
							$mycmdhtml='<i class="fas fa-file-import import_ap_lianikis" title="'.gks_lang('Εισαγωγή ως έξοδο').'"></i>';
							$mycmdhtml.=' <a href="' .$mytext. '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'.gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα').'"></i></a>';
						} else if (startwith($mytext,'https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo') or 
						           startwith($mytext,'https://mydataapidev.aade.gr/TimologioQR/QRInfo')) {
							$mycmdhtml='<i class="fas fa-truck open_admin_aade_delivery_note" title="'.gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής').'"></i>';
							$mycmdhtml.=' <a href="' .$mytext. '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'.gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα').'"></i></a>';
						
						} else if (startwith($mytext,'https://') or startwith($mytext,'http://')) {
							$mycmdhtml='<a href="' .$mytext. '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'.gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα').'"></i></a>';
						} else {
		    		
						} 
						$mycmdhtml.='<i class="fas fa-trash-alt delete_scan" title="'.gks_lang('Διαγραφή').'"></i>';
						$myaa++;
						$html='<div class="row gks_itemrow" data-aa="' . $myaa . '" data-recid="'.$row['id_qrcode_scan'].'">' .
		      				'<div class="col-sm-6 decodedText">' .
		      				 	$mytext .
		      				'</div>' .
		      				'<div class="col-sm-3 d-flex align-items-center justify-content-center">' .
		      				 	'<div style="text-align:center">' . $row['app_id'].'<br>'.$row['format'] . '</div>' . 
		      				'</div>' .
		      				'<div class="col-sm-3 d-flex align-items-center justify-content-end">' .
		      				 	$mycmdhtml .
		      				'</div>' .
		      				'<div class="col-sm-12 import_result" style="display:none;">' .
		      				'</div>' .
		      			'</div>';						
						echo $html;
					}
					?>
				</div>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ρυθμίσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('settings');?>> 
          <div class="form-group row">

            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="company_id_sub_id" class="form-control form-control-sm">
                <option value="0|0"></option>
                <?php
                
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$def_company_qrcode) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
							<small class="form-text text-muted"><?php echo gks_lang('Αφορά την εισαγωγή των αποδείξεων λιανικής ως έξοδο');?></small>
                            
            </div>
	        </div>   
        </div>
      </div>
      
            
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('page');?>        

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εφαρμογή σάρωσης QR Code για κινητό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('mobile');?>> 
					<div class="row">
			    	<div class="col-12">
							<?php echo gks_lang('H εφαρμογή gks ERP App Mobile λειτουργεί σε κινητά Android και σας επιτρέπει να σαρώνεται πιο εύκολα κωδικούς QR Code και να εμφανίζονται σε αυτήν την σελίδα οι σαρώσεις αυτόματα');?>
				  	</div>
				  </div>
					
				  <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
				    <div class="col-6">
				    	<img src="/my/img/gks_ERP_App_Mobile1.jpg" style="max-width: 100%;border: 1px solid lightgray;">
				    </div>
				    <div class="col-6">
				    	<img src="/my/img/gks_ERP_App_Mobile2.jpg" style="max-width: 100%;border: 1px solid lightgray;">
				    </div>
				    <div class="col-6" style="margin-top: 30px;">
				    	<img src="/my/img/gks_ERP_App_Mobile3.jpg" style="max-width: 100%;border: 1px solid lightgray;">
				    </div>
				    <div class="col-6" style="margin-top: 30px;">
				    	<img src="/my/img/gks_ERP_App_Mobile4.jpg" style="max-width: 100%;border: 1px solid lightgray;">
				    </div>
				    						
				  </div>
				  <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
			    	<div class="col-12">
			    		<?php echo gks_lang('Μπορείτε να κατεβάσετε την εφαρμογή και να την εγκαταστήσετε στο κινητό σας από τον σύνδεσμο');?>:<br>
			    		<a href="<?php echo $GKS_ERP_APP_MOBILE_VER_link;?>"><?php echo $GKS_ERP_APP_MOBILE_VER_link;?></a>
			    	</div>
				  </div>
				  <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
			    	<div class="col-12" style="text-align:center;">
			    		<img src="<?php echo $GKS_ERP_APP_MOBILE_VER_img;?>" style="max-width:100%;"/>
			    	</div>
				  </div>
            	
        </div>
      </div>
      
    </div>
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δημιουργία QR Code');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('mobile');?>> 
					<div class="form-group row">
            <label for="qr_url" class="col-sm-12 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('URL-text');?>:</label>
            <div class="col-sm-12">
            	<textarea id="qr_url" class="form-control form-control-sm" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="min-height:100px"></textarea>
            	
            </div>
          </div>
					<div class="form-group row">
          	<div class="col-sm-12" id="qr_result" style="text-align:center;">
          		
          	</div>
          </div>
        </div>
      </div>
		</div>
  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','delete',0);?>;

var myaa=<?php echo $myaa;?>;
var last_recid =<?php echo $last_recid;?>;

var mychange = 'change keyup paste';

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  function docReady(fn) {
	  // see if DOM is already available
	  if (document.readyState === "complete"
	      || document.readyState === "interactive") {
	      // call on next available tick
	      setTimeout(fn, 1);
	  } else {
	      document.addEventListener("DOMContentLoaded", fn);
	  }
  }

  
  
  function gks_render_item(myaa,decodedText,formatName,app_id,recid) {
  	mycmdhtml='';
  	if (decodedText.startsWith('https://www1.aade.gr/tameiakes/myweb/q1.php') || 
  	    decodedText.startsWith('https://www1.gsis.gr/tameiakes/myweb/q1.php')) {
  		mycmdhtml='<i class="fas fa-file-import import_ap_lianikis" title="'+gks_lang('Εισαγωγή ως έξοδο')+'"></i>';
  		mycmdhtml+=' <a href="' + decodedText + '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'+gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα')+'"></i></a>';
  	} else if (decodedText.startsWith('https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo') || 
  	           decodedText.startsWith('https://mydataapidev.aade.gr/TimologioQR/QRInfo')) {
  		mycmdhtml='<i class="fas fa-truck open_admin_aade_delivery_note" title="'+gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής')+'"></i>';
  		mycmdhtml+=' <a href="' + decodedText + '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'+gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα')+'"></i></a>';
      
  	} else if (decodedText.startsWith('https://') || 
  	           decodedText.startsWith('http://')) {
  		mycmdhtml='<a href="' + decodedText + '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'+gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα')+'"></i></a>';
  	} else {
  		
  	} 
  	mycmdhtml+='<i class="fas fa-trash-alt delete_scan" title="'+gks_lang('Διαγραφή')+'"></i>';
  	
  	html='<div class="row gks_itemrow" data-aa="' + myaa + '" data-recid="' + recid + '">' +
  				'<div class="col-sm-6 decodedText">' +
  					decodedText +
  				'</div>' +
  				'<div class="col-sm-3 d-flex align-items-center justify-content-center">' +
  					'<div style="text-align:center">' + app_id + '<br>' + formatName + '</div>' + 
  				'</div>' +
  				'<div class="col-sm-3 d-flex align-items-center justify-content-end">' +
  					mycmdhtml +
  				'</div>' +
  				'<div class="col-sm-12 import_result" style="display:none;">' +
  				'</div>' +
  			'</div>';
  	
  	$('#gks_item_list').prepend(html);
  	$('.gks_itemrow[data-aa=' + myaa + '] .import_ap_lianikis').click(gks_import_ap_lianikis_click);
  	$('.gks_itemrow[data-aa=' + myaa + '] .open_admin_aade_delivery_note').click(gks_open_admin_aade_delivery_note_click);
  	
  	$('.gks_itemrow[data-aa=' + myaa + '] .delete_scan').click(gks_delete_scan_click);
  
  	mydict[decodedText]=myaa;
	
  }
  
  var lastResult=''; 	var timer_clear; var mydict=[];
  docReady(function () {
    var resultContainer = document.getElementById('qr-reader-results');
    function onScanSuccess(decodedText, decodedResult) {
	    if (decodedText !== lastResult) {
	    	lastResult = decodedText;
	    	if (mydict[decodedText] === undefined) {
				myaa++;
				gks_render_item(myaa,decodedText,decodedResult.result.format.formatName,gks_lang('Σελίδα'),0);
				gks_qrcode_cmd(myaa,'add',decodedText,decodedResult.result.format.formatName);	 
				
	    	  //console.log(mydict);
	    	  //console.log(decodedText, decodedResult);
	    	} else {
	    		//console.log(mydict[decodedText]);
	    		$('.gks_itemrow[data-aa=' + mydict[decodedText] + ']').css({'background-color':'yellow'}).animate({backgroundColor:'unset'}, 2000);
	    	}
	    	timer_clear = setTimeout(timer_clear_run, 3000);
      }
    }

    var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
    
  });
  function timer_clear_run() {
		//console.log('clear ' + lastResult);  	
		lastResult='';
  }
  
  function gks_import_ap_lianikis_click() {
  	aa=$(this).parent().parent().attr('data-aa'); if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	myurl=$('.gks_itemrow[data-aa=' + aa + '] .decodedText').text();
  	//console.log(aa);
  	cid=$('#company_id_sub_id').val();
  	if (cid=='' || cid=='0|0') {
  		myalert('error:'+gks_lang('Επιλέξτε μια εταιρία την οποία αφορά η απόδειξη'));	
  		return;
  	}
  	
  	if ($('.gks_itemrow[data-aa=' + aa + '] .import_ap_lianikis').hasClass('fa-hourglass')) return;
  	$('.gks_itemrow[data-aa=' + aa + '] .import_result').slideUp();
  	$('.gks_itemrow[data-aa=' + aa + '] .import_ap_lianikis').addClass('fa-hourglass').removeClass('fa-file-import');
  	
  	datasend='';
  	datasend+='&cid=' + encodeURIComponent($.base64.encode(cid));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(myurl));
  	
    $.ajax({
			url: '/my/admin-qrcode-import.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_aa: aa,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>').slideDown();
				$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_ap_lianikis').addClass('fa-file-import').removeClass('fa-hourglass');
			},				
			success: function(data) {
				if (!data) {
					$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>').slideDown();
					$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_ap_lianikis').addClass('fa-file-import').removeClass('fa-hourglass');
				} else {
					if (data.success == true) {
						$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_result').html('<div class="alert alert-success" role="alert">'+gks_lang('Επιτυχής εισαγωγή')+'<br>'+gks_lang('Το παραστατικό είναι το')+' '+$.base64.decode(data.message) + '</div>').slideDown();
						$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_ap_lianikis').addClass('fa-file-import').removeClass('fa-hourglass');
					} else {
          	$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_result').html('<div class="alert alert-danger" role="alert">' + $.base64.decode(data.message) + '</div>').slideDown();
          	$('.gks_itemrow[data-aa=' + this.gks_aa + '] .import_ap_lianikis').addClass('fa-file-import').removeClass('fa-hourglass');
          }
        }
      }
    });  	
  }
  function gks_open_admin_aade_delivery_note_click(event) {
  	aa=$(this).parent().parent().attr('data-aa'); if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	myurl=$('.gks_itemrow[data-aa=' + aa + '] .decodedText').text();
    //console.log(aa,myurl);
    urlopen='/my/admin-aade-delivery-note.php?cmd=status&qrurl='+encodeURIComponent(myurl);

    if (event.ctrlKey) {
      window.open(urlopen, '_blank').focus();
    } else {
      location.href=urlopen; 
    }
    
  }
  
  function gks_delete_scan_click() {
  	aa=$(this).parent().parent().attr('data-aa'); if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
	
	  //$('.gks_itemrow[data-aa=' + aa + ']').remove();
	  gks_qrcode_cmd(aa,'delete','','');
  }
  
  function gks_qrcode_cmd(aa,cmd,url,format) {
	
		elem=$('.gks_itemrow[data-aa=' + aa + ']');
		aa=elem.attr('data-aa'); if (isNaN(aa)) aa=0; //if (aa<=0) return;
		recid=elem.attr('data-recid'); if (isNaN(recid)) recid=0; 
	 
	  //console.log(aa,recid);
  	datasend='';
  	datasend+='&cmd=' + encodeURIComponent($.base64.encode(cmd));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(url));
  	datasend+='&format=' + encodeURIComponent($.base64.encode(format));
  	datasend+='&aa=' + encodeURIComponent(aa+'');
  	datasend+='&recid=' + encodeURIComponent(recid+'');
  	datasend+='&last_recid=' + encodeURIComponent(last_recid+'');
 
	
    $.ajax({
			url: '/my/admin-qrcode-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_cmd: cmd,
			gks_url: url,
			gks_aa: aa,
			gks_recid: recid,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
						if (this.gks_cmd=='delete') {
							$('.gks_itemrow[data-aa=' + this.gks_aa + ']').remove();
						} else if (this.gks_cmd=='add') {
							$('.gks_itemrow[data-aa=' + this.gks_aa + ']').attr('data-recid',data.ret_recid);
						} else if (this.gks_cmd=='new') {
							for(i=0; i<data.recs.length;i++) {
								if (last_recid<data.recs[i].recid) last_recid= data.recs[i].recid;
								myaa++;
								gks_render_item(myaa,data.recs[i].url,data.recs[i].format,data.recs[i].app_id,data.recs[i].recid);
								
							}
						}
						
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		
    });
	
  }
  
  $('.gks_itemrow .import_ap_lianikis').click(gks_import_ap_lianikis_click);
  $('.gks_itemrow .open_admin_aade_delivery_note').click(gks_open_admin_aade_delivery_note_click);
  $('.gks_itemrow .delete_scan').click(gks_delete_scan_click);
   
  function gks_qrcode_get_new_recs() {
	  //console.log('gks_qrcode_get_new_recs');
	  gks_qrcode_cmd(0,'new','','');
  }
  setInterval(gks_qrcode_get_new_recs, 3000);
  
  $('#qr_url').on(mychange,function() {
  	url=$('#qr_url').val();
  	//console.log(url);
  	$('#qr_result').html('<img src="/my/img/wait.gif">');
    
  	datasend='';
  	datasend+='&cmd=' + encodeURIComponent($.base64.encode('createqr'));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(url));

    $.ajax({
			url: '/my/admin-qrcode-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+ jqXHR.responseText + '</div>')
			},				
			success: function(data) {
				if (!data) {
					$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				} else {
					if (data.success == true) {
						$('#qr_result').html($.base64.decode(data.qrhtml));
						
					} else {
						$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
					}
				}
			}
		
    });
      		
  });

  function qr_url_change() {gks_resize_textarea($(this));}
  $('#qr_url').on(mychange, qr_url_change);
  gks_resize_textarea($('#qr_url'));
    
});
</script>





<?php
//db_close();
include_once('_my_footer_admin.php');


