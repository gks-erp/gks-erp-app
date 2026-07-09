<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Σημείου Ενδιαφέροντος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_poi_ids=gks_permission_user_condition($my_wp_user_id,'gks_poi','01');



if ($id>0) {
  $sql="select * from gks_poi where id_poi=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}


$poi_descr=''; if (isset($_POST['poi_descr'])) $poi_descr=trim_gks(base64_decode($_POST['poi_descr']));
$poi_type_id=''; if (isset($_POST['poi_type_id'])) $poi_type_id=intval($_POST['poi_type_id']);
$poi_locode=''; if (isset($_POST['poi_locode'])) $poi_locode=trim_gks(base64_decode($_POST['poi_locode']));
$poi_iata_code=''; if (isset($_POST['poi_iata_code'])) $poi_iata_code=trim_gks(base64_decode($_POST['poi_iata_code']));
$poi_icao_code=''; if (isset($_POST['poi_icao_code'])) $poi_icao_code=trim_gks(base64_decode($_POST['poi_icao_code']));
$poi_parent_id=''; if (isset($_POST['poi_parent_id'])) $poi_parent_id=intval($_POST['poi_parent_id']);
$poi_phone=''; if (isset($_POST['poi_phone'])) $poi_phone=trim_gks(base64_decode($_POST['poi_phone']));
$poi_email=''; if (isset($_POST['poi_email'])) $poi_email=trim_gks(base64_decode($_POST['poi_email']));
$poi_website=''; if (isset($_POST['poi_website'])) $poi_website=trim_gks(base64_decode($_POST['poi_website']));
$poi_odos=''; if (isset($_POST['poi_odos'])) $poi_odos=trim_gks(base64_decode($_POST['poi_odos']));
$poi_arithmos=''; if (isset($_POST['poi_arithmos'])) $poi_arithmos=trim_gks(base64_decode($_POST['poi_arithmos']));
$poi_orofos=''; if (isset($_POST['poi_orofos'])) $poi_orofos=trim_gks(base64_decode($_POST['poi_orofos']));
$poi_perioxi=''; if (isset($_POST['poi_perioxi'])) $poi_perioxi=trim_gks(base64_decode($_POST['poi_perioxi']));
$poi_poli=''; if (isset($_POST['poi_poli'])) $poi_poli=trim_gks(base64_decode($_POST['poi_poli']));
$poi_tk=''; if (isset($_POST['poi_tk'])) $poi_tk=trim_gks(base64_decode($_POST['poi_tk']));
$poi_country_id=0; if (isset($_POST['poi_country_id'])) $poi_country_id=intval($_POST['poi_country_id']);
$poi_nomos_id=0; if (isset($_POST['poi_nomos_id'])) $poi_nomos_id=intval($_POST['poi_nomos_id']);
$poi_map_latitude=0; if (isset($_POST['poi_map_latitude'])) $poi_map_latitude=floatval(str_replace(',','.', $_POST['poi_map_latitude']));
$poi_map_longitude=0; if (isset($_POST['poi_map_longitude'])) $poi_map_longitude=floatval(str_replace(',','.', $_POST['poi_map_longitude']));
$poi_disable=0; if (isset($_POST['poi_disable'])) $poi_disable=intval($_POST['poi_disable']);
$poi_color=''; if (isset($_POST['poi_color'])) $poi_color=trim_gks(base64_decode($_POST['poi_color']));
$poi_comments=''; if (isset($_POST['poi_comments'])) $poi_comments=trim_gks(base64_decode($_POST['poi_comments']));


$user_companys=gks_get_companys_list();
$poi_company_id=0;
$poi_company_sub_id=0;
$poi_company_id_sub_id=''; if (isset($_POST['poi_company_id_sub_id'])) $poi_company_id_sub_id=trim_gks(base64_decode($_POST['poi_company_id_sub_id']));
if ($poi_company_id_sub_id!='') {
  $parts=explode('|',$poi_company_id_sub_id);
  if (count($parts)==2) {
    $poi_company_id=intval($parts[0]);
    $poi_company_sub_id=intval($parts[1]);
    $found=false;
    foreach ($user_companys as $value) {
      if ($value['id_company'] == $poi_company_id and $value['id_company_sub'] == $poi_company_sub_id) {
        $found=true;
        break;
      }
    }
    //echo 'ggg'.$poi_company_id.'|'.$poi_company_sub_id;die();

    if ($found==false) {$poi_company_id=0;$poi_company_sub_id=0;}
  }
}

$poi_parastatiko_apodiji_journal_id=0;
$poi_parastatiko_apodiji_seira_id=0;
$poi_parastatiko_timologio_journal_id=0;
$poi_parastatiko_timologio_seira_id=0;
if ($poi_company_id>0)  {
  if (isset($_POST['poi_parastatiko_apodiji_journal_id']))    $poi_parastatiko_apodiji_journal_id   =intval($_POST['poi_parastatiko_apodiji_journal_id']);
  if (isset($_POST['poi_parastatiko_apodiji_seira_id']))      $poi_parastatiko_apodiji_seira_id     =intval($_POST['poi_parastatiko_apodiji_seira_id']);
  if (isset($_POST['poi_parastatiko_timologio_journal_id']))  $poi_parastatiko_timologio_journal_id =intval($_POST['poi_parastatiko_timologio_journal_id']);
  if (isset($_POST['poi_parastatiko_timologio_seira_id']))    $poi_parastatiko_timologio_seira_id   =intval($_POST['poi_parastatiko_timologio_seira_id']);
  
  
}

//echo $poi_company_id.'|'.$poi_company_sub_id;die();

$myareas_str = trim_gks(base64_decode($_POST['myareas_str']));
$myareas_array='nochange';
//echo '<pre>'.$myareas_str;die();
if (!($myareas_str=='' or $myareas_str=='nochange')) {
  $myareas_array = json_decode($myareas_str, true);
  if ($myareas_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['myareas_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $bounds_str = trim_gks(base64_decode($_POST['bounds_str']));
  $bounds_array = json_decode($bounds_str, true);
  if ($bounds_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['bounds_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  
}

//echo '<pre>';print_r($myareas_array);die();


if ($poi_descr=='') {debug_mail(false,'emptyl',                  gks_lang('Η περιγραφή ΔΕΝ μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή ΔΕΝ μπορεί να είναι κενή')));
  echo json_encode($return); die();}


if ($poi_type_id==0) {debug_mail(false,'emptyl',                 gks_lang('Ο τύπος δεν μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τύπος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die();}

if (in_array($poi_type_id,[1,3])==false) $poi_locode=''; //location
if (in_array($poi_type_id,[2,4])==false) $poi_iata_code=''; //aerodromio
if (in_array($poi_type_id,[2,4])==false) $poi_icao_code=''; //aerodromio
  

//$sql="select * from gks_poi where poi_descr like '".$db_link->escape_string($poi_descr)."' and id_poi<>".$id;
//$result = $db_link->query($sql);  
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die(); } 
//if ($result->num_rows>=1) {
//  $row = $result->fetch_assoc();
//  $message=str_replace('[1]',$poi_descr,gks_lang('Το Σημείο Ενδιαφέροντος με τίτλο <b>[1]</b> υπάρχει ήδη')).':'.
//  '<br><a href="admin-poi-item.php?id='.$row['id_poi'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
//  debug_mail(false,'poi exist symbol',$message);
//  $return = array('success' => false, 'message' => base64_encode($message));
//  echo json_encode($return); die();
//}

if ($poi_email != '' and !filter_var($poi_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,gks_lang('To email δεν είναι σωστό').' : '.$poi_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

 

  
if ($poi_country_id==0) {debug_mail(false,'emptyl',              gks_lang('Επιλέξτε μία χώρα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
  echo json_encode($return); die();}

//if ($poi_nomos_id==0) {debug_mail(false,'emptyl',                gks_lang('Επιλέξτε έναν νομό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}






$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_poi');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_poi (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-poi-item.php?id='.$id); 
}



$sql="update gks_poi set 
poi_descr='".$db_link->escape_string($poi_descr)."',
poi_type_id=".$poi_type_id.",
poi_locode='".$db_link->escape_string($poi_locode)."',
poi_iata_code='".$db_link->escape_string($poi_iata_code)."',
poi_icao_code='".$db_link->escape_string($poi_icao_code)."',
poi_parent_id=".$poi_parent_id.",
poi_phone=". ($poi_phone =='' ? 'null' : "'".$db_link->escape_string($poi_phone)."'").",
poi_email=". ($poi_email =='' ? 'null' : "'".$db_link->escape_string($poi_email)."'").",
poi_website=". ($poi_website =='' ? 'null' : "'".$db_link->escape_string($poi_website)."'").",
poi_odos=". ($poi_odos =='' ? 'null' : "'".$db_link->escape_string($poi_odos)."'").",
poi_arithmos=". ($poi_arithmos =='' ? 'null' : "'".$db_link->escape_string($poi_arithmos)."'").",
poi_orofos=". ($poi_orofos =='' ? 'null' : "'".$db_link->escape_string($poi_orofos)."'").",
poi_perioxi=". ($poi_perioxi =='' ? 'null' : "'".$db_link->escape_string($poi_perioxi)."'").",
poi_poli=". ($poi_poli =='' ? 'null' : "'".$db_link->escape_string($poi_poli)."'").",
poi_tk=". ($poi_tk =='' ? 'null' : "'".$db_link->escape_string($poi_tk)."'").",
poi_country_id=".$poi_country_id.",
poi_nomos_id=".$poi_nomos_id.",

poi_map_latitude='".number_format($poi_map_latitude,16,'.','')."',
poi_map_longitude='".number_format($poi_map_longitude,16,'.','')."',

poi_disable=".$poi_disable.",
poi_color=". ($poi_color =='' ? 'null' : "'".$db_link->escape_string($poi_color)."'").",
poi_comments='".$db_link->escape_string($poi_comments)."',
poi_company_id=".$poi_company_id.",
poi_company_sub_id=".$poi_company_sub_id.",

poi_parastatiko_apodiji_journal_id=".$poi_parastatiko_apodiji_journal_id.",
poi_parastatiko_apodiji_seira_id=".$poi_parastatiko_apodiji_seira_id.",
poi_parastatiko_timologio_journal_id=".$poi_parastatiko_timologio_journal_id.",
poi_parastatiko_timologio_seira_id=".$poi_parastatiko_timologio_seira_id.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_poi = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }




$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_poi',$id);

if (is_array($myareas_array)) {
  
  //print '<pre>';print_r($bounds_array);die();
  
  $sql="update gks_poi set 
  poi_areas='".$db_link->escape_string(serialize($myareas_array))."',
  poi_bound_north=".floatval($bounds_array['north']).",
  poi_bound_south=".floatval($bounds_array['south']).",
  poi_bound_east=".floatval($bounds_array['east']).",
  poi_bound_west=".floatval($bounds_array['west'])."
  where id_poi = ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
}

$ret_run=gks_sociallinks_item_save($_POST,'gks_poi',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







