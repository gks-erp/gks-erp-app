<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');
$my_page_title=gks_lang('Μετάβαση στην Alpha Bank');

//if ($my_wp_user_id == 0) {header('Location: basket.php'); die();}

//if (!isset($_gks_session['gks']['confirm'])) {header('Location: basket.php'); die();}

if (!isset($_gks_session['gks']['alphabank']['id_object']) or 
    !isset($_gks_session['gks']['alphabank']['order_guid']) or 
    !isset($_gks_session['gks']['alphabank']['pliroteo']) ) {
  header('Location: basket.php'); die();      
}

$my_page_title=gks_lang('Πληρωμή μέσω τράπεζας');
db_open();
stat_record();


$my_alpha=my_alphabank_settings();

$mid=$my_alpha['mid'];                  
$lang='el';                     
$orderid = $_gks_session['gks']['alphabank']['id_object'].'x'.$_gks_session['gks']['alphabank']['order_guid']; //$_gks_session['gks']['alphabank']['id_object'].'guid'.
$orderDesc=$GKS_SITE_NAME . ' '.gks_lang('Παραγγελία').' '.$_gks_session['gks']['alphabank']['id_object'];
$orderAmount= number_format($_gks_session['gks']['alphabank']['pliroteo'],2,'.','');
$currency='EUR';
$user_email = $_gks_session['gks']['basket']['user']['email'];
$billCountry='Greece';
$billState='Thessaloniki';
$billZip='57013';
$billCity='Oraiokastro';
$billAddress='Ikaron1B';
$payMethod='auto:MasterPass';
$trType='1';
$extInstallmentoffset='0';
$extInstallmentperiod='6';
$confirmUrl=GKS_SITE_URL.'/my/alphabank-success.php';
$cancelUrl=GKS_SITE_URL.'/my/alphabank-cancel.php';
$var2=$_gks_session['gks']['alphabank']['id_object'];

$sharedsecretkey=
//$mystr.=$sharedsecretkey;

        

$alphabank_url = $my_alpha['url'];



$extInstallmentperiod=6;


if($extInstallmentperiod > 1) {
    $form_data_array = array(
        'mid' => $my_alpha['mid'],
        'lang' => $lang,
        'orderid' => $_gks_session['gks']['alphabank']['id_object'].'x'.$_gks_session['gks']['alphabank']['order_guid'],
        'orderDesc' => $orderDesc,
        'orderAmount' => $orderAmount,
        'currency' => $currency,
        'payerEmail' => $user_email,
        'billCountry' => $billCountry,
        'billState' => $billState, 
        'billZip' => $billZip,
        'billCity' => $billCity,
        'billAddress' => $billAddress,
        'payMethod' => $payMethod,
        'trType' => $trType,
        'extInstallmentoffset' => $extInstallmentoffset,
        'extInstallmentperiod' => $extInstallmentperiod,
        'confirmUrl' => $confirmUrl,
        'cancelUrl' => $cancelUrl,
        'var2' =>  $_gks_session['gks']['alphabank']['id_object'],
    );
} else {
    $form_data_array = array(
        'mid' => $my_alpha['mid'],
        'lang' => $lang,
        'orderid' => $_gks_session['gks']['alphabank']['id_object'].'x'.$_gks_session['gks']['alphabank']['order_guid'],
        'orderDesc' => $orderDesc,
        'orderAmount' => $orderAmount,
        'currency' => $currency,
        'payerEmail' => $user_email,
        'billCountry' => $billCountry,
        'billState' => $billState,
        'billZip' => $billZip,
        'billCity' => $billCity,
        'billAddress' => $billAddress,
        'payMethod' => $payMethod,
        'trType' => $trType,
        'confirmUrl' => $confirmUrl,
        'cancelUrl' => $cancelUrl,
        'var2' =>  $_gks_session['gks']['alphabank']['id_object'],
    );
}


$mystr=implode('', $form_data_array);

$mystr = iconv('utf-8', 'utf-8//IGNORE', $mystr). $my_alpha['shared_secret_key'];   
echo  $mystr;
     
$digest = base64_encode(sha1($mystr, true));

            
    


$sql="update gks_payments_alphabank set request2_json='".$db_link->escape_string($mystr)."', 
status ='try 1',
digest1='".$db_link->escape_string($digest)."'
where id_payment_alphabank=".$_gks_session['gks']['basket']['payment']['table_id'];
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
}

require_once('_my_header_empty.php');
//echo $gkIP;

?>
<script language="javascript" type="text/javascript">
   var originalJQuery=jQuery;
   var originalJQuerySign=$;
</script>

<link href="/my/css/gks_frontend.css" rel="stylesheet" type="text/css"/>
<link href="/my/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="/my/css/jquery-ui.min.css" rel="stylesheet">
<link href="/my/css/jquery-ui.structure.min.css" rel="stylesheet">
<link href="/my/css/jquery-ui.theme.min.css" rel="stylesheet">
<link href="/my/css/gks_frontend_fontawesome-all.css" rel="stylesheet">

<script src="/my/js/jquery-3.3.1.min.js"></script>
<script src="/my/js/jquery-ui.min.js"></script>
<script src="/my/js/jquery.base64.js"></script>
<script src='/my/js/jquery.datetimepicker.full.min.js' type='text/javascript'></script>
<script src='/my/js/my.js' type='text/javascript'></script>

<link rel="stylesheet" type="text/css" href="/my/css/tooltipster-noir.css"/>
<link rel="stylesheet" type="text/css" href="/my/css/tooltipster.css"/>
<script type='text/javascript' src="/my/js/tooltipster-3.0/js/jquery.tooltipster.min.js"></script>


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
          <div style="font-size:10pt; padding:20px 10px 20px 10px;border: 0px solid #ddd;text-align:center;"> 
            <p ><span style="font-size:24pt"><?php echo gks_lang('Παρακαλώ περιμένετε');?>...</span></p>
            <?php
            $ftype='hidden';
            if  ($gkIP=='192.168.1.202') $ftype='hidden1';
            ?>
            <form method="get" action="<?php echo $alphabank_url;?>" style="text-align: center;width:100%" enctype="application/x-www-form-urlencoded" accept-charset="UTF-8">
              <input type="<?php echo $ftype;?>" name="mid" value="<?php echo $mid;?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="lang" value="<?php echo $lang;?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="orderid" value="<?php echo $orderid;?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="orderDesc" value="<?php echo $orderDesc;?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="orderAmount" value="<?php echo $orderAmount; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="currency" value="<?php echo $currency; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="payerEmail" value="<?php echo $user_email; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="billCountry" value="<?php echo $billCountry; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="billState" value="<?php echo $billState; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="billZip" value="<?php echo $billZip; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="billCity" value="<?php echo $billCity; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="billAddress" value="<?php echo $billAddress; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="payMethod" value="<?php echo $payMethod; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="trType" value="<?php echo $trType; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="extInstallmentoffset" value="<?php echo $extInstallmentoffset; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="extInstallmentperiod" value="<?php echo $extInstallmentperiod; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="confirmUrl" value="<?php echo $confirmUrl; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="cancelUrl" value="<?php echo $cancelUrl; ?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="var2" value="<?php echo $var2;?>" style="display:block;text-align: center;width: 100%;">
              <input type="<?php echo $ftype;?>" name="digest" value="<?php echo $digest; ?>" style="display:block;text-align: center;width: 100%;">
              <input id="mybutton" class="btn btn-primary" type="submit" value="" style="text-align: center;">
            </form>            
            
          </div>

        </div>
      </div><!-- .gks_row --> 
    </div><!--.gks_container-->
  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->
 


<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

 
jQuery3(document).ready(function($) {
<?php if ($ftype=='hidden') {  ?>
  //$("body").addClass("myloading");
  //$('#mybutton').trigger('click');
<?php } ?>  
});


</script>  

<div class="gks_waitmodal"></div>     
<?php
require_once('_my_footer_empty.php');



