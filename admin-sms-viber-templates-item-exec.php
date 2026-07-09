<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


//die();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση πρότυπου κειμένου για SMS-Viber').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms_viber_template',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_sms_viber_template where id_sms_viber_template = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$sms_viber_template_name=''; if (isset($_POST['sms_viber_template_name'])) $sms_viber_template_name=trim_gks(base64_decode($_POST['sms_viber_template_name']));
$sms_viber_template_text=''; if (isset($_POST['sms_viber_template_text'])) $sms_viber_template_text=trim_gks(base64_decode($_POST['sms_viber_template_text']));
$sms_viber_template_sortorder=0; if (isset($_POST['sms_viber_template_sortorder'])) $sms_viber_template_sortorder=intval($_POST['sms_viber_template_sortorder']);
$sms_viber_template_disabled=0; if (isset($_POST['sms_viber_template_disabled'])) $sms_viber_template_disabled=intval($_POST['sms_viber_template_disabled']);
$sms_enabled=0; if (isset($_POST['sms_enabled'])) $sms_enabled=intval($_POST['sms_enabled']);
$viber_enabled=0; if (isset($_POST['viber_enabled'])) $viber_enabled=intval($_POST['viber_enabled']);


if ($sms_viber_template_name=='') {debug_mail(false,'emptyl',           gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }


$fobjects=''; if (isset($_POST['fobjects'])) $fobjects=trim_gks(base64_decode($_POST['fobjects']));

$fobjects_parts=explode(']][[',$fobjects);
$fobjects_texts=array();
if (count($fobjects_parts)>0) {
  foreach ($fobjects_parts as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      $fobjects_texts[]="'".$db_link->escape_string($value)."'";
    }
  }
}
$fobjects_ids=array();
if (count($fobjects_texts)>0) {
  $sql_fobjects="select id_sms_viber_template_object from gks_sms_viber_template_object where object_descr in (".implode(',',$fobjects_texts).")";
  $result_fobjects = $db_link->query($sql_fobjects);        
  if (!$result_fobjects) {
    debug_mail(false,'error sql',$sql_fobjects);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row_fobjects = $result_fobjects->fetch_assoc()) {
    $fobjects_ids[]=$row_fobjects['id_sms_viber_template_object'];
  }
}
//print '<pre>'.$fobjects;print_r($fobjects_texts); print_r($fobjects_ids); die();



$sql="select * from gks_sms_viber_template where sms_viber_template_name like '".$db_link->escape_string($sms_viber_template_name)."' and id_sms_viber_template<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-sms-viber-templates-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$sms_viber_template_text,$message);
  $message=str_replace('[2]',row['id_sms_viber_template'],$message);
  
  debug_mail(false,'monada metrisis exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$parameterline_array_str = trim_gks(base64_decode($_POST['parameterline_array_str']));

$parameterline_array = json_decode($parameterline_array_str, true);
if ($parameterline_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['parameterline_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//print '<pre>'; print_r($parameterline_array); die();

$parameterline_array_clean=[];
foreach ($parameterline_array as $value) {
  if (isset($value['label']) and trim_gks($value['label'])!='' and 
      isset($value['type']) and ($value['type']=='text' or $value['type']=='textarea')) {
  
    $item=[];
    $item['type']=$value['type'];
    $item['label']=trim_gks($value['label']);
    $item['id']='sms_param_'.$item['label'];
    if (isset($value['px']) and trim_gks($value['px'])!='')
      $item['px']=trim_gks($value['px']);
    if (isset($value['icon']) and trim_gks($value['icon'])!='')   $item['icon']=trim_gks($value['icon']);
    if (isset($value['value']) and trim_gks($value['value'])!='') $item['value']=trim_gks($value['value']);
    if (isset($value['jquery_selector']) and trim_gks($value['jquery_selector'])!='') $item['jquery_selector']=trim_gks($value['jquery_selector']);
    
    $parameterline_array_clean[]=$item;
    
  }
} 
//print '<pre>'; print_r($parameterline_array_clean); die();

$other_fields='';
if (count($parameterline_array_clean)>0) {
  $other_fields=json_encode($parameterline_array_clean,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}



$redirect='';
if ($id==-1) {
  $sql="insert into gks_sms_viber_template (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-sms-viber-templates-item.php?id='.$id); 
}

$sql="update gks_sms_viber_template set 
sms_viber_template_text='".$db_link->escape_string($sms_viber_template_text)."',
sms_viber_template_name='".$db_link->escape_string($sms_viber_template_name)."',
sms_viber_template_sortorder=".$sms_viber_template_sortorder.",
sms_viber_template_disabled=".$sms_viber_template_disabled.",
sms_enabled=".$sms_enabled.",
viber_enabled=".$viber_enabled.",
other_fields='".$db_link->escape_string($other_fields)."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_sms_viber_template = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$sql="delete from gks_sms_viber_template_object_forms where sms_viber_template_id=".$id;
if (count($fobjects_ids)>0) $sql.=" and sms_viber_template_object_id not in (".implode(',',$fobjects_ids).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }


foreach ($fobjects_ids as $value) {
  $sql="select * from gks_sms_viber_template_object_forms where sms_viber_template_id=".$id." and sms_viber_template_object_id=".$value;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_sms_viber_template_object_forms (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      sms_viber_template_id,sms_viber_template_object_id
    ) values (
      now(), now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",".$value."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
} 


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

