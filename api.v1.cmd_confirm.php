<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_confirm($id_hotel,$row_hotel,$input_data) {
  global $db_link;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  
  global $GKS_SITE_EMAIL;

  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  //print '<pre>|'.$gks_erp_cookie_id.'|';
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  if (isset($input_data['post']['ui_lang']) and trim_gks($input_data['post']['ui_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['post']['ui_lang']);
  }
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);
  //global $gks_lang_array;
  
  //return $input_data['shortcode_attributes']['lang'].'|'.$_gks_session['gks']['ui_lang'];



  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  $my_wp_user_id=0; //get_current_user_id();





  if (isset($_gks_session['gks']['confirm'])==false or isset($_gks_session['gks']['confirm']['id_object']) == false or $_gks_session['gks']['confirm']['id_object']==0) {
  $out='
<script language="javascript" type="text/javascript">
   window.location.href="/";
</script>';   
  
    return $out;
  }
  
  $out='';  
  $out.='
<script language="javascript" type="text/javascript">
   var originalJQuery=jQuery;
   var originalJQuerySign=$;
</script>

<link href="'.GKS_SITE_URL.'my/css/gks_frontend.css?v='.$gks_cache_version.'" rel="stylesheet" type="text/css"/>
<link href="'.GKS_SITE_URL.'my/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.structure.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.theme.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/gks_frontend_fontawesome-all.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/hotel.css?v='.$gks_cache_version.'" rel="stylesheet">
<script src="'.GKS_SITE_URL.'my/js/jquery-3.3.1.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery-ui.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery.base64.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery.datetimepicker.full.min.js" type="text/javascript"></script>
<script src="'.GKS_SITE_URL.'my/js/my.js?v='.$gks_cache_version.'" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/css/tooltipster-noir.css"/>
<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/css/tooltipster.css"/>
<script type="text/javascript" src="'.GKS_SITE_URL.'my/js/tooltipster-3.0/js/jquery.tooltipster.min.js"></script>

<script language="javascript" type="text/javascript">
    var jQuery3=jQuery;
    window.jQuery =originalJQuery;
    window.$ = originalJQuerySign;
</script>

<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div class="gks_container">
      <div class="gks_row">
        <div style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
          <div style="float: left;width:40%;min-width:250px; font-size:10pt; padding:20px 10px 20px 0px;border: 0px solid #ddd;"> 
            <p ><span style="font-size:24pt">'.gks_lang('Επιβεβαιωμένη Παραγγελία').'<br></span></p>
          </div>
          <div style="float: left;width:60%;min-width:250px;text-align: right !important;padding:20px 0px 0px 0px;">          
            <span style="background-color: #476b14;" class="gks_basket_button" id="header_basket_show">'.gks_lang('Επισκόπηση Παραγγελίας').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #476b14;" class="gks_basket_button" id="header_basket_checkout">'.gks_lang('Αποστολή & Χρέωση').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #476b14;" class="gks_basket_button" id="header_basket_pay">'.gks_lang('Πληρωμή').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #73AD21;" class="gks_basket_button" id="header_basket_confirm">'.gks_lang('Επιβεβαίωση').'</span>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div class="gks_container">
      <div class="gks_row">
        <div style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
      ';     
  

  
  $out.='<h1 data-fontsize="46" data-lineheight="47.84px" class="fusion-responsive-typography-calculated" style="--fontSize:46; line-height: 1.04;">'.gks_lang('Η παραγγελία σας έχει καταχωρηθεί επιτυχώς').'</h1>';
  //I paraggelia exei lifthei epityxos
  $out.='<h2 data-fontsize="36" data-lineheight="37px"    class="fusion-responsive-typography-calculated" style="--fontSize:36; line-height: 1.04;">'.gks_lang('Αριθμός παραγγελίας').': <b><u>#'.$_gks_session['gks']['confirm']['id_object'].'</u></b></h1>';
  //arithmos paraggelias:
  
  if ($_gks_session['gks']['confirm']['payment_acquirer_type'] == 'bank') {
  
    
    $out.='<p>'.
    gks_lang('Για να προχωρήσει η παραγγελία σας θα πρέπει να κάνετε κατάθεση του ποσού').
    ': <b><u>'.myCurrencyFormat($_gks_session['gks']['confirm']['poso']).'</u></b> '.
    gks_lang('σε έναν από τους παρακάτω τραπεζικούς λογαριασμούς').
    ':<br>'.
    '<b><u>'.gks_get_list_bank_accounts().'</u></b><br>';

    
    
    if (isset($_gks_session['gks']['confirm']['bank9digit']) and $_gks_session['gks']['confirm']['bank9digit']!='') {
      $out.=gks_lang('Κατά την διαδικασία της κατάθεσης, ορίστε στην Αιτιολογία τον αριθμό').
      ' <b><u>'.$_gks_session['gks']['confirm']['bank9digit'].'</u></b> '.
      gks_lang('έτσι ώστε να μπορέσουμε να ταυτοποιήσουμε την κατάθεση με την συγκεκριμένη παραγγελία');
    }
    $out.='</p>';
    
    $out.='<p>'.
    gks_lang('Εάν υπάρχουν τυχόν έξοδα για την μεταφορά των χρημάτων, θα πρέπει να τα επιβαρυνθείτε εσείς').'<br>'.
    gks_lang('Δεν θα εκτελεστεί η παραγγελία σας εάν δεν συμφωνεί το τελικό ποσό').'<br>'.
    gks_lang('Στείλτε μας το αποδεικτικό κατάθεσης με email στο').
    ' <b><a href="'.$GKS_SITE_EMAIL.'">'.$GKS_SITE_EMAIL.'</a></b></p>';
    $out.='<p>'.gks_lang('Θα ενημερωθείτε με email ή/και με SMS για την εξέλιξη της παραγγελίας').'</p>';
    
  } else {
    
    //
  }
  
  

  $out.='<p>&nbsp;</p>';
  
//  $out.='<p>'.gks_lang('Αγαπητέ πελάτη,').'</p>'.
//        '<p>'.str_replace('%1', $_gks_session['gks']['confirm']['id_object'], gks_lang('Σας ενημερώνουμε ότι η παραγγελία σας με ID %1 έχει ολοκληρωθεί με επιτυχία')).'</p>'.
//        '<p>'.gks_lang('Παρακαλούμε ενεργοποιήστε την άδεια της εφαρμογής').'</p>'.
//        '<p>'.gks_lang('Σε περίπτωση που δεν έχετε &#34;κατεβάσει&#34; ακόμη την εφαρμογή, μπορείτε να το κάνετε από την σελίδα').' '.
//        ' <a href="/download'.gks_set_lang_url().'">'.gks_lang('Λήψη').'</a></p>'.
//        '<p>'.gks_lang('Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email').' '.
//        '<a href="mailto:info@easyfilesselection.com">info@easyfilesselection.com</a>'.' '.
//        gks_lang('ή στη φόρμα επικοινωνίας της ιστοσελίδας μας').' '.
//        '<a href="/contact">'.gks_lang('Επικοινωνία').'</a>'.
//        '</p>'.
//        '<p>'.gks_lang('Σας  ευχαριστούμε που κάνατε την αγορά σας από τον ιστότοπο μας').'</p>'.
//        '<p>'.gks_lang('Με εκτίμηση<br>Η ομάδα της εφαρμογής').' '.$hotel_title.'</p>';

     
   


$out.='
        </div>
      </div>
    </div>
  </div>
</div>
';


  //if (GKS_DEBUG == false and 1==1) {
    $tropos_pliromis_temp=$_gks_session['gks']['basket']['tropos_pliromis'];
    $user_temp=$_gks_session['gks']['basket']['user'];
    unset($_gks_session['gks']['basket']);
    unset($_gks_session['gks']['basket']['hotel']['reservation']);
    unset($_gks_session['gks']['confirm']);
    unset($_gks_session['gks']['alphabank']);  
    unset($_gks_session['gks']['paypal']);  
    unset($_gks_session['gks']['payment_error']);

    gks_erp_cookie_defaults();  
    
    $_gks_session['gks']['basket']['user']=$user_temp;
    $_gks_session['gks']['basket']['tropos_pliromis']=$tropos_pliromis_temp;
  //}
  






  gks_erp_cookie_save($gks_erp_cookie_id);

      
  return '<div id="gks_hotel_container">'.$out.'</div>';
  
}

