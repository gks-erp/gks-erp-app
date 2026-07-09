<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
https://test.easyfilesselection.com/my/admin-aade-delivery-note.php?mark=400001958400461&cmd=register
https://test.easyfilesselection.com/my/admin-aade-delivery-note.php?qrurl=https%3A%2F%2Fmydataapidev.aade.gr%2FTimologioQR%2FQRInfo%3Fq%3D6BjD5UfgGAVhLFN3PDbWSghZ9T8wdbY3%252b077%252b4%252bUmQFMynlhS2LO0uMeX8KggcozFV1UEOkt8YT7b0%252bytDvR%252bFbqcWpXeEXia55dEqk3%252bO0%253d&cmd=register
https://test.easyfilesselection.com/my/admin-aade-delivery-note.php?mark=400001958400461&qrurl=https%3A%2F%2Fmydataapidev.aade.gr%2FTimologioQR%2FQRInfo%3Fq%3D6BjD5UfgGAVhLFN3PDbWSghZ9T8wdbY3%252b077%252b4%252bUmQFMynlhS2LO0uMeX8KggcozFV1UEOkt8YT7b0%252bytDvR%252bFbqcWpXeEXia55dEqk3%252bO0%253d&cmd=register
*/
//echo '<pre>';
//echo rawurlencode('https://mydataapidev.aade.gr/TimologioQR/QRInfo?q=6BjD5UfgGAVhLFN3PDbWSghZ9T8wdbY3%2b077%2b4%2bUmQFMynlhS2LO0uMeX8KggcozFV1UEOkt8YT7b0%2bytDvR%2bFbqcWpXeEXia55dEqk3%2bO0%3d');
//echo "\r\n";
//echo urlencode('https://mydataapidev.aade.gr/TimologioQR/QRInfo?q=6BjD5UfgGAVhLFN3PDbWSghZ9T8wdbY3%2b077%2b4%2bUmQFMynlhS2LO0uMeX8KggcozFV1UEOkt8YT7b0%2bytDvR%2bFbqcWpXeEXia55dEqk3%2bO0%3d');
//die();

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής');
$nav_active_array=array('warehouse','warehouse_aade_delivery_note');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade_delivery_note','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

//print '<pre>';print_r($gks_user_settings);die();

$user_companys=gks_get_companys_list();
$def_company='0|0'; if (isset($gks_user_settings['gks_aade_delivery_note']['def_company'])) $def_company=$gks_user_settings['gks_aade_delivery_note']['def_company'];
//print '<pre>aaa '.$def_company;die();
//print '<pre>aaa ';print_r($user_companys);die();




include_once('_my_header_admin.php');
?>
<link href="css/admin-aade-delivery-note.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/scan.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<script src="js/html5-qrcode.min.js"></script>


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
          <?php echo gks_lang('Δελτίο αποστολής - Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('scan');?>> 
          
          <div class="form-group row">
            <label for="input_mark" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΜΑΡΚ');?>:<span id="needs_mark"> (<span><i class="fas fa-asterisk"></i></span>)</span></label>
            <div class="col-md-8">
              <input id="input_mark" type="text" class="form-control form-control-sm myneedsave" value="<?php 
              $def_mark='';if (isset($_GET['mark'])) $def_mark=trim($_GET['mark']);
              echo $def_mark;
              ?>" placeholder="<?php echo gks_lang('π.χ.');?> 400001958277843" autocomplete="off" style="margin-bottom: 10px;"/>
              <button id="mark_get_qrUrl" class="btn btn-sm1 btn-primary" disabled style="margin-bottom:10px;"><?php echo gks_lang('Λήψη QRCode URL από το ΜΑΡΚ');?></button>
              <div id="mark_get_qrUrl_results"></div>  
              
              
            </div>
          </div>
          <div class="form-group row">
            <label for="input_qrUrl" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('QRCode URL');?>:<span id="needs_qrUrl" style="display:none;">  (<span style="color:red;"><i class="fas fa-asterisk"></i></span>)</span></label>
            <div class="col-md-8">
              <textarea id="input_qrUrl" type="text" class="form-control form-control-sm myneedsave" placeholder="<?php echo gks_lang('π.χ.');?> https://mydataapidev.aade.gr/TimologioQR/QRInfo?q=6BjD5..." style="margin-bottom:10px;min-height: 100px;" disabled><?php
                if (isset($_GET['qrurl'])) {
                  $temp=$_GET['qrurl'];
                  //$temp=str_replace('+', '%2b', $temp);
                  
                  echo $temp;
                }
                ?></textarea>
              <button id="qrUrl_get_mark" class="btn btn-sm1 btn-primary" disabled style="margin-bottom:10px;"><?php echo gks_lang('Λήψη ΜΑΡΚ από το QRCode URL');?></button>
              <button id="qrUrl_open_newtab" class="btn btn-sm1 btn-primary" disabled style="margin-bottom:10px;"><?php echo gks_lang('Άνοιγμα συνδέσμου σε νέα καρτέλα');?></button>
              <div id="qrUrl_get_mark_results"></div>  
            </div>
            
          </div>
          
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
            
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <button id="scan_start" class="btn btn-sm1 btn-primary" style="margin-bottom:10px;"><?php echo gks_lang('Σάρωση QRCode');?></button>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12" style="margin-bottom:10px;">
    			    <div id="qr-reader" style="width:100%"></div>
    			    <div id="qr-reader-results"></div>              
            </div>
          </div>
                              
        </div>
      </div>

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
						if (startwith($mytext,'https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo') or 
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
          <?php echo gks_lang('Καταχωρήσεις στο gks ERP');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('scan');?>> 
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button id="run_cmd_search" class="btn btn-sm1 btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
            </div>
	        </div>          
          <div class="form-group row">
            <div class="col-md-12" id="records_html">
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ενέργειες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cmds');?>> 
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave">
                <option value="0|0"></option>
                <?php
                if (count($user_companys)==1) {
                  foreach ($user_companys as $row_select) {
                    $def_company=$row_select['id'];
                  }
                }
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$def_company) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
							<small class="form-text text-muted"><?php echo gks_lang('Ποια εταιρεία αφορά η συγκεκριμένη ενέργεια');?></small>
            </div>
	        </div>

          <div class="form-group row">
            <label for="gks_deltio_cmd" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενέργεια');?>:</label>
            <div class="col-md-8">
              <?php
              $def_cmd='';
              if (isset($_GET['cmd'])) $def_cmd=trim($_GET['cmd']);
              ?>
              <select id="gks_deltio_cmd" class="form-control form-control-sm myneedsave">
                <option value="status"   <?php if ($def_cmd=='status')   echo 'selected';?>><?php echo gks_lang('Προβολή κατάστασης (GetDeliveryNoteStatus)');?></option>
                <option value="register" <?php if ($def_cmd=='register') echo 'selected';?>><?php echo gks_lang('Έναρξη διακίνησης (RegisterTransfer)');?></option>
                <option value="confirm"  <?php if ($def_cmd=='confirm')  echo 'selected';?>><?php echo gks_lang('Δηλώση του αποτέλεσματος της παράδοσης (ConfirmDeliveryOutcome)');?></option>
                <option value="reject"   <?php if ($def_cmd=='reject')   echo 'selected';?>><?php echo gks_lang('Ολική απόρριψη του Δελτίου Αποστολής (RejectDeliveryNote)');?></option>
              </select>
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_info" id="gks_deltio_cmd_info_status" style="display1:none;">
            <div class="col-md-12" >
              <b><?php echo gks_lang('Διαδικασία λήψης της κατάστασης και του ιστορικού ενός Δελτίου Αποστολής.');?></b>
              <br><br>
    	        <?php echo gks_lang('Η εντολή επιστρέφει την τρέχουσα κατάσταση (status) και το ιστορικό (lifecycleHistory) του δελτίου.');?>
    	        <br>
    	        <?php echo gks_lang('Επιτρέπεται στον εκδότη, τον λήπτη και σε οποιονδήποτε μεταφορέα συμμετείχε στη διακίνηση.');?>
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_info" id="gks_deltio_cmd_info_register" style="display:none;">
            <div class="col-md-12">
              <b><?php echo gks_lang('Διαδικασία δήλωσης έναρξης ή μεταφόρτωσης διακίνησης από μεταφορέα');?></b>
              <br><br>
    	        <?php echo gks_lang('Η εντολή καλείται από τον μεταφορέα για να δηλώσει την παραλαβή των αγαθών και την έναρξη της διακίνησης, ή την παραλαβή από προηγούμενο μεταφορέα (μεταφόρτωση).');?>
              <br>
              <?php echo gks_lang('Με την επιτυχή κλήση, το Δελτίο Αποστολής μεταβαίνει σε κατάσταση <b>InTransit</b>.');?>
              <br>
              <?php echo gks_lang('Σε περίπτωση επιτυχίας, η απόκριση περιέχει το <b>transferMark</b>, το οποίο είναι ο Μοναδικός Αριθμός Καταχώρησης του γεγονότος μεταφοράς.');?>
            </div>
	        </div>      
          <div class="form-group row gks_deltio_cmd_info" id="gks_deltio_cmd_info_confirm" style="display:none;">
            <div class="col-md-12">
              <b><?php echo gks_lang('Διαδικασία δήλωσης αποτελέσματος παράδοσης από μεταφορέα ή λήπτη');?>.</b>
              <br><br>
    	        <?php echo gks_lang('Η μέθοδος καλείται είτε από τον Μεταφορέα για να δηλώσει το αποτέλεσμα της παράδοσης, είτε από τον Λήπτη για να επιβεβαιώσει την παραλαβή.');?>
    	        <br>
    	        <?php echo gks_lang('Αν κληθεί από Μεταφορέα σε B2B συναλλαγή, θέτει το ΔΑ σε κατάσταση <b>DeliveredByCarrier</b>.');?>
              <br>
              <?php echo gks_lang('Αν κληθεί από Μεταφορέα σε B2C συναλλαγή, θέτει το ΔΑ σε κατάσταση <b>Completed</b>.');?>
              <br>
              <?php echo gks_lang('Αν κληθεί από Λήπτη, θέτει το ΔΑ σε κατάσταση <b>Completed.</b>');?>
              <br>
              <?php echo gks_lang('Η τιμή <b>NONE</b> για το πεδίο outcome θέτει το ΔΑ σε κατάσταση <b>FailedDelivery</b>.');?>
            </div>
	        </div>  
          <div class="form-group row gks_deltio_cmd_info" id="gks_deltio_cmd_info_reject" style="display:none;">
            <div class="col-md-12">
              <b><?php echo gks_lang('Διαδικασία ολικής απόρριψης διακίνησης από τον λήπτη.');?></b>
              <br><br>
    	        <?php echo gks_lang('Η μέθοδος καλείται αποκλειστικά από τον Λήπτη για να δηλώσει την ολική <b>απόρριψη</b> των ειδών του Δελτίου Αποστολής.');?>
              <br>
              <?php echo gks_lang('Με την επιτυχή κλήση, το Δελτίο Αποστολής μεταβαίνει στην τελική κατάσταση <b>Rejected</b>.');?>
              <br>
              <?php echo gks_lang('Σε περίπτωση επιτυχίας, η απόκριση περιέχει το <b>rejectMark</b>, το οποίο είναι ο Μοναδικός Αριθμός Καταχώρησης του γεγονότος απόρριψης.');?>
            </div>
	        </div>

          <div class="form-group row gks_deltio_cmd_params_status" style="display1:none;">
            <div class="col-md-12 gks_deltio_cmd_params_title"><?php echo gks_lang('Παράμετροι');?></div>            
	        </div>
          <div class="form-group row gks_deltio_cmd_params_status" style="display1:none;">
            <label for="params_status_issuerVatNumber" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Το ΑΦΜ του εκδότη');?>:</label>
            <div class="col-md-8">
              <input id="params_status_issuerVatNumber" type="text" class="form-control form-control-sm myneedsave" value="" placeholder="" autocomplete="off">
              <small class="form-text text-muted"><?php echo gks_lang('Απαιτείται αν ο καλών δεν είναι ο εκδότης');?></small>
            </div>
	        </div>


          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <div class="col-md-12 gks_deltio_cmd_params_title"><?php echo gks_lang('Παράμετροι');?></div>            
	        </div>
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_vehicleNumber" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Μεταφορικού Μέσου');?>: (<span style="color:red"><i class="fas fa-asterisk"></i></span>)</label>
            <div class="col-md-8">
              <input id="params_register_vehicleNumber" type="text" class="form-control form-control-sm myneedsave" value="" placeholder="<?php echo gks_lang('π.χ.');?> ΑΒΕ1234" autocomplete="off">
              <small class="form-text text-muted"><?php echo gks_lang('Αριθμός κυκλοφορίας/Όνομα πλωτού μέσου/Κωδικός Δρομολογίου ή πτήσης/Διακίνηση άνευ Μεταφορικού Μέσου');?></small>
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_transportType" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Είδος Μεταφορικού Μέσου');?>: (<span style="color:red"><i class="fas fa-asterisk"></i></span>)</label>
            <div class="col-md-8">
              <select id="params_register_transportType" class="form-control form-control-sm myneedsave">
                <option value="1"><?php echo getAADE_TransportTypeDescr(1);?></option>
                <option value="2"><?php echo getAADE_TransportTypeDescr(2);?></option>
                <option value="3"><?php echo getAADE_TransportTypeDescr(3);?></option>
                <option value="4"><?php echo getAADE_TransportTypeDescr(4);?></option>
                <option value="5"><?php echo getAADE_TransportTypeDescr(5);?></option>
                <option value="6"><?php echo getAADE_TransportTypeDescr(6);?></option>
                <option value="7"><?php echo getAADE_TransportTypeDescr(7);?></option>
              </select>
            </div>
	        </div>
	        <!--
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_timeStamp" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρονοσφραγίδα');?>:</label>
            <div class="col-md-8">
              <input id="params_register_timeStamp" type="text" class="form-control form-control-sm myneedsave" value="<?php
              echo showDate(time(), 'd/m/Y H:i', 1);
              ?>" placeholder="" autocomplete="off">
            </div>
	        </div>
	        -->
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_carrierVatNumber" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΦΜ Μεταφορικής Εταιρείας');?>: (<span style="color:red"><i class="fas fa-asterisk"></i></span>)</label>
            <div class="col-md-8">
              <input id="params_register_carrierVatNumber" type="text" class="form-control form-control-sm myneedsave" value="" placeholder="<?php echo gks_lang('π.χ.');?> 123456789" autocomplete="off">
            </div>  
	        </div>
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_pNumber" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός κυκλοφορίας <b>Ρ</b>');?>:</label>
            <div class="col-md-8">
              <input id="params_register_pNumber" type="text" class="form-control form-control-sm myneedsave" value="" placeholder="" autocomplete="off">
              <small class="form-text text-muted"><?php echo gks_lang('Αριθμός κυκλοφορίας του επικαθήμενου/ρυμουλκούμενου οχήματος');?></small>
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <div class="col-md-12" style="text-align:center"><?php echo gks_lang('Τοποθεσία Μεταφόρτωσης');?></div>
            <label for="params_register_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="params_register_longitude" type="number" class="form-control form-control-sm myneedsave" value="" placeholder="<?php echo gks_lang('π.χ.');?> 40.12345" autocomplete="off">
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_params_register" style="display:none;">
            <label for="params_register_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="params_register_latitude" type="number" class="form-control form-control-sm myneedsave" value="" placeholder="<?php echo gks_lang('π.χ.');?> 22.12345" autocomplete="off">
            </div>
	        </div>	        




          <div class="form-group row gks_deltio_cmd_params_confirm" style="display:none;">
            <div class="col-md-12 gks_deltio_cmd_params_title"><?php echo gks_lang('Παράμετροι');?></div>            
	        </div>
          <div class="form-group row gks_deltio_cmd_params_confirm" style="display:none;">
            <label for="params_confirm_outcome" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Το αποτέλεσμα της παράδοσης');?>: (<span style="color:red"><i class="fas fa-asterisk"></i></span>)</label>
            <div class="col-md-8">
              <select id="params_confirm_outcome" class="form-control form-control-sm myneedsave">
                <option value="FULL"><?php echo getAADE_lch_outcome('FULL');?></option>
                <option value="PARTIAL"><?php echo getAADE_lch_outcome('PARTIAL');?></option>
                <option value="NONE"><?php echo getAADE_lch_outcome('NONE');?></option>
              </select>
            </div>
	        </div>
          <div class="form-group row gks_deltio_cmd_params_confirm" style="display:none;">
            <label for="params_confirm_deliveredWithoutRecipient" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εάν η παράδοση έγινε χωρίς την παρουσία του παραλήπτη');?>:</label>
            <div class="col-md-8">
              <input id="params_confirm_deliveredWithoutRecipient" type="checkbox" value="1" class="switchery1_this"/>
            </div>
	        </div>	        
          <div id="params_confirm_deliveredPackaging_div">
          <div class="form-group row gks_deltio_cmd_params_confirm" style="display:none;">
            <label for="params_confirm_deliveredPackaging" class="col-md-12 col-form-label form-control-sm text-md-right1"><?php echo gks_lang('Λίστα με τις συσκευασίες και τις ποσότητες που παραδόθηκαν');?>:</label>
            <div class="col-md-12">
              <div id="params_confirm_deliveredPackaging">
                <div class="form-group row gks_eidos_label">
                  <div class="col-4 gks_items_col">
                    <div class="table-dark gks_eidos_label"><?php echo gks_lang('Είδος Συσκευασίας');?></div>
                  </div>
                  <div class="col-3 gks_items_col">
                    <div class="table-dark gks_eidos_label"><?php echo gks_lang('Πλήθος');?></div>
                  </div>
                  <div class="col-3 gks_items_col">
                    <div class="table-dark gks_eidos_label"><?php echo gks_lang('Τίτλος για Λοιπά');?></div>
                  </div>                  
                  <div class="col-2 gks_items_col">
                    <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
                  </div>
                </div>
                <div class="params_confirm_deliveredPackaging_list">
                  
                </div>
              </div>
            </div>
          </div>
	        </div>	


          <div class="form-group row gks_deltio_cmd_params_reject" style="display:none;">
            <div class="col-md-12 gks_deltio_cmd_params_title"><?php echo gks_lang('Παράμετροι');?></div>            
	        </div>
          <div class="form-group row gks_deltio_cmd_params_reject" style="display:none;">
            <label for="params_reject_rejectionReason" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή του λόγου απόρριψης');?>:</label>
            <div class="col-md-8">
              <input id="params_reject_rejectionReason" type="text" class="form-control form-control-sm myneedsave" value="" placeholder="" autocomplete="off">
            </div>
	        </div>	        
	        
	        

	        
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button id="run_cmd" class="btn btn-sm1 btn-primary"><?php echo gks_lang('Εκτέλεση');?></button>
            </div>
	        </div>
	        
	        <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
	        
          <div class="form-group row">
            <div class="col-md-12">
              <div id="result_html">
                
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          Raw Data
        </div>
        <div class="card-body" <?php echo gks_card_body('rawdata');?>>       
          <div class="form-group row">
            <div class="col-md-12">
              <div id="result_raw_data_send" style="background-color: #efefef;">
                
              </div>
              <div id="result_raw_data_response" style="background-color: #efefef;">
                
              </div>
            </div>
	        </div>      
        </div>
      </div>
            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('history');?>> 
          <div class="form-group row">
            <div class="col-md-12" id="history_card">
            </div>
          </div>
        </div>
      </div>
      
            
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Διάγραμμα καταστάσεων');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('help');?>> 
          <div id="lightgallery_imgs">
            <a class="lightgallery_img" href="img/aade_delivery_note.png" data-sub-html="<?php echo gks_lang('Διάγραμμα καταστάσεων');?>">
              <img src="img/aade_delivery_note.png" style="width:100%" class="">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>        


<?php

$packagingTypes=[];
for ($i = 1; $i <= $getAADE_PackagingTypeDescr_max; $i++) {
  $packagingTypes[]=array('id' => $i, 'descr' => getAADE_PackagingTypeDescr($i));
}

?>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings_users','delete',0);?>;

var from_php_packagingTypes=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($packagingTypes));?>'));

var myaa=<?php echo $myaa;?>;
var last_recid =<?php echo $last_recid;?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

});

</script>

<script src="js/admin-aade-delivery-note.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


