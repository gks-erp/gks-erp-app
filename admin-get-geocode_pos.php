<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αναζήτηση geocode_pos');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_tk','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$odos='';       if (isset($_POST['odos']))        $odos=trim_gks(base64_decode($_POST['odos']));
$arithmos='';   if (isset($_POST['arithmos']))    $arithmos=trim_gks(base64_decode($_POST['arithmos']));
$orofos='';     if (isset($_POST['orofos']))      $orofos=trim_gks(base64_decode($_POST['orofos']));
$perioxi='';    if (isset($_POST['perioxi']))     $perioxi=trim_gks(base64_decode($_POST['perioxi']));
$poli='';       if (isset($_POST['poli']))        $poli=trim_gks(base64_decode($_POST['poli']));
$tk='';         if (isset($_POST['tk']))          $tk=trim_gks(base64_decode($_POST['tk']));
$nomos_id=0;    if (isset($_POST['nomos_id']))    $nomos_id=intval($_POST['nomos_id']);
$country_id=0;  if (isset($_POST['country_id']))  $country_id=intval($_POST['country_id']);

$nomos_descr='';
if ($nomos_id>0) {
  $sql="select nomos_descr FROM gks_nomoi where id_nomos=".$nomos_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $nomos_descr=trim_gks($row['nomos_descr']);
  }
}


$country_name='';
if ($country_id>0) {
  $sql="select country_name FROM gks_country where id_country=".$country_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $country_name=trim_gks($row['country_name']);
  }
}
           
$address='';
if ($odos!='') $address.=$odos.' ';
if ($arithmos!='') $address.=$arithmos;
$address=trim($address);
if ($address!='') $address.=',';


if ($perioxi!='' && $perioxi!=$odos) $address.=$perioxi.',';
if ($poli!='' && $poli!=$odos && $poli!=$perioxi) $address.=$poli.',';
if ($tk!='' && $tk!=$odos && $tk!=$perioxi && $tk!=$poli) $address.=$tk.',';
if ($nomos_descr!='' && $nomos_descr!=$odos && $nomos_descr!=$perioxi && $nomos_descr!=$poli && $nomos_descr!=$tk) $address.=$nomos_descr.',';
if ($country_name!='' && $country_name!=$odos && $country_name!=$perioxi && $country_name!=$poli && $country_name!=$tk && $country_name!=$nomos_descr) $address.=$country_name.',';

if ($address!='') $address=substr($address, 0, strlen($address)-1);

//echo '<pre>'.$address;die();

if (strlen($address)<3) {
  debug_mail(false,'address is empty',$address);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η διέθυνση που γράψατε')));
  echo json_encode($return); die();}  

$sql="select lat,lng from gks_cache_googlemaps_address where address like '".$db_link->escape_string($address)."' order by id_address desc limit 1";
$result = $db_link->query($sql);   
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $pos=array(
    'lat'=>floatval($row['lat']),
    'lng'=>floatval($row['lng']),
  );
  $return = array('success' => true, 'message' => base64_encode('OK'), 'pos' => $pos);
  echo json_encode($return); die();
}


if ($GKS_GOOGLE_MAPS_API_KEY_SERVER=='') {
  debug_mail(false,'GKS_GOOGLE_MAPS_API_KEY_SERVER is empty',$GKS_GOOGLE_MAPS_API_KEY_SERVER);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το κλειδί για το <b>Google Maps Api Key</b> από τις ρυθμίσεις της <a href="admin-system-settings.php">εφαρμογής</a>')));
  echo json_encode($return); die();}  
  
$geocode_url='https://maps.googleapis.com/maps/api/geocode/json?address='.
rawurlencode($address).
'&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geocode_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
$response_str=curl_exec($ch);
curl_close($ch);

//echo'<pre>ssss33';die();
  
  
if ($response_str!='') {
  $response=json_decode($response_str, true);  
  //echo '<pre>';print_r($response);die();
/*{
   "error_message" : "This IP, site or mobile application is not authorized to use this API key. Request received from IP address 2a02:587:493b:9800:2f52:7658:1dd4:4abb, with empty referer",
   "results" : [],
   "status" : "REQUEST_DENIED"
}*/  
  
  
  if (is_array($response)) {
    if (isset($response['error_message']) and $response['error_message']!='') {
      debug_mail(false,'geocode_url error',$response_str);
      $return = array('success' => false, 'message' => base64_encode($response['error_message']));
      echo json_encode($return); die();
    } else if (isset($response['results'][0]['geometry']['location']['lat']) and isset($response['results'][0]['geometry']['location']['lng'])) {
      $pos=array(
        'lat'=>floatval($response['results'][0]['geometry']['location']['lat']),
        'lng'=>floatval($response['results'][0]['geometry']['location']['lng']),
      );
      
      $sql="insert into gks_cache_googlemaps_address (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      address,lat,lng,response
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($address)."',
      ".number_format($pos['lat'], 12, '.', '').",
      ".number_format($pos['lng'], 12, '.', '').",
      '".$db_link->escape_string($response_str)."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}

      $return = array('success' => true, 'message' => base64_encode('OK'), 'pos' => $pos);
      echo json_encode($return); die();
    } 
  }
}

if ($response_str=='') {
  debug_mail(false,'empty response',$geocode_url);
  $response_str=gks_lang('Μήπως η IP του gks ERP δεν είναι στις επιτρεπτές στο App της Google ;');
}

$return = array('success' => false, 'message' => base64_encode(gks_lang('Γενικό Σφάλμα').': '.$response_str));
echo json_encode($return); die();
