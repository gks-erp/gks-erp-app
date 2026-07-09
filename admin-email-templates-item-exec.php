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
$my_page_title=gks_lang('Αποθήκευση Πρότυπου email');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email_template',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}







if ($id>0) {
  $sql="select * from gks_email_template where id_email_template=".$id." limit 1";
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



$preview=0; if (isset($_POST['preview'])) $preview=intval($_POST['preview']);
$fobject_sel=''; if (isset($_POST['fobject_sel'])) $fobject_sel=trim_gks(base64_decode($_POST['fobject_sel']));
$fobject_id=0; if (isset($_POST['fobject_id'])) $fobject_id=intval($_POST['fobject_id']);


$email_template_descr=''; if (isset($_POST['email_template_descr'])) $email_template_descr=trim_gks(base64_decode($_POST['email_template_descr']));
if ($preview==1 and $email_template_descr=='') $email_template_descr='draft '.time();

$gks_lang='el-GR'; if (isset($_POST['gks_lang'])) $gks_lang=trim_gks(base64_decode($_POST['gks_lang']));
$edit_mode=''; if (isset($_POST['edit_mode'])) $edit_mode=trim_gks(base64_decode($_POST['edit_mode']));
if ($edit_mode!='html' and $edit_mode!='raw') $edit_mode='html';


//print '<pre>';print $file_type; die();
$email_body='';    if (isset($_POST['email_body']))       $email_body=trim_gks(base64_decode($_POST['email_body']));
$email_subject=''; if (isset($_POST['email_subject']))    $email_subject=trim_gks(base64_decode($_POST['email_subject']));
$email_message='';    if (isset($_POST['email_message'])) $email_message=trim_gks(base64_decode($_POST['email_message']));

$is_disable=0; if (isset($_POST['is_disable'])) $is_disable=intval($_POST['is_disable']);
$need_attachments=0; if (isset($_POST['need_attachments'])) $need_attachments=intval($_POST['need_attachments']);
$sortorder=0; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);

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
  $sql_fobjects="select id_email_template_object from gks_email_template_object where object_descr in (".implode(',',$fobjects_texts).")";
  $result_fobjects = $db_link->query($sql_fobjects);        
  if (!$result_fobjects) {
    debug_mail(false,'error sql',$sql_fobjects);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row_fobjects = $result_fobjects->fetch_assoc()) {
    $fobjects_ids[]=$row_fobjects['id_email_template_object'];
  }
}
//print '<pre>'.$fobjects;print_r($fobjects_texts); print_r($fobjects_ids); die();







$loc_langs=''; if (isset($_POST['loc_langs'])) $loc_langs=trim_gks(base64_decode($_POST['loc_langs']));
$loc_langs_array=array();
if ($loc_langs!='') $loc_langs_array=json_decode($loc_langs, true);
//print '<pre>'; print_r($loc_langs_array); die();

$loc_langs_array_clean=array();
foreach ($loc_langs_array as $value) {
  if (isset($value['lang']) and isset($value['form_id'])) {
    $lang=trim_gks($value['lang']);
    $form_id=intval($value['form_id']);
    if ($lang!='' and $form_id>0 and $lang!=$gks_lang and isset($loc_langs_array_clean[$lang])==false) {
      $loc_langs_array_clean[$lang]=$form_id;
    }
  }
} 
//print '<pre>'; print_r($loc_langs_array_clean); die();


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
    $item['id']='email_param_'.$item['label'];
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



$attach_paramline_array_str = trim_gks(base64_decode($_POST['attach_paramline_array_str']));

$attach_paramline_array = json_decode($attach_paramline_array_str, true);
if ($attach_paramline_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['attach_paramline_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//print '<pre>'; print_r($attach_paramline_array); die();

$attach_errors=[];
$attach_paramline_array_clean=[];
foreach ($attach_paramline_array as $value) {
  if (isset($value['basefolder']) and trim_gks($value['basefolder'])!='' and 
      isset($value['relative_path']) and $value['relative_path']!='') {
    
    $base_path='';
    $mybasefolder=$value['basefolder'];
    if ($mybasefolder=='erplo') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site';
    else if ($mybasefolder=='erpfi') $base_path=substr(GKS_FileServerShare,0,strlen(GKS_FileServerShare)-1);
    else if ($mybasefolder=='erpul') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads';
    else if ($mybasefolder=='erpdl') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install';
    else if ($mybasefolder=='wodpr') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/uploads';
    else $attach_errors[]=gks_lang('To mybasefolder είναι κενό');
    
    $base_name='';
    if ($mybasefolder=='erplo') $base_name=gks_lang('ERP Λογότυπα');
    else if ($mybasefolder=='erpfi') $base_name=gks_lang('ERP Αρχεία');
    else if ($mybasefolder=='erpul') $base_name=gks_lang('ERP Μεταφορτώσεις');
    else if ($mybasefolder=='erpdl') $base_name=gks_lang('ERP Λήψεις');
    else if ($mybasefolder=='wodpr') $base_name=gks_lang('Wordpress');
    
    if ($base_path!='') {
      if (file_exists($base_path)==false or is_dir($base_path)==false) {
        $attach_errors[]=gks_lang('Δεν υπάρχει ο φάκελος').': <b>'.$base_name.'</b> '.$base_path;
      }
      $relative_path=$value['relative_path'];
      if (file_exists($base_path.'/'.$relative_path)==false or is_file($base_path.'/'.$relative_path)==false) {
        $attach_errors[]=gks_lang('Δεν βρέθηκε το αρχείο').': <b>'.$base_path.'/'.$relative_path.'</b>';
      }  
    

      $item=[];
      $item['basefolder']=trim_gks($value['basefolder']);
      $item['relative_path']=trim_gks($value['relative_path']);
      $item['name_for_email']=trim_gks($value['name_for_email']);
      $item['def_check']=intval($value['def_check'])==1;
  
      $attach_paramline_array_clean[]=$item;
    }
  }
}
//print '<pre>'; print_r($attach_paramline_array_clean); die();
if (count($attach_errors)>0) {
  debug_mail(false,'attach_errors',print_r($attach_errors,true)."\r\n".print_r($attach_paramline_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα με τα συνημμένα').':<br>'.implode('<br>',$attach_errors)));
  echo json_encode($return); die();}



$attachments='';
if (count($attach_paramline_array_clean)>0) {
  $attachments=json_encode($attach_paramline_array_clean,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
 
if ($email_template_descr=='') {debug_mail(false,'emptyl',       gks_lang('Ορίστε την Περιγραφή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή')));
  echo json_encode($return); die();}

if ($email_subject=='') {debug_mail(false,'emptyl',              gks_lang('Ορίστε τo Θέμα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τo Θέμα')));
  echo json_encode($return); die();}


$sql="select * from gks_email_template where email_template_descr like '".$db_link->escape_string($email_template_descr)."' and id_email_template<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το πρότυπο email με όνομα <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$email_template_descr,$message);
  debug_mail(false,'already exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}











$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_email_template');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_email_template (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-email-templates-item.php?id='.$id); 
}

$localization_set_id=0;
while (true) {
  $localization_set_id=rand(1000000,1999999);
  $sql = "SELECT localization_set_id from gks_email_template where localization_set_id=".$localization_set_id;
  $result = $db_link->query($sql);
  if ($result->num_rows == 0) {
    break;
  }
}



$sql="update gks_email_template set 
email_template_descr='".$db_link->escape_string($email_template_descr)."',
gks_lang='".$db_link->escape_string($gks_lang)."',
edit_mode='".$db_link->escape_string($edit_mode)."',

email_body='".$db_link->escape_string($email_body)."',
email_subject='".$db_link->escape_string($email_subject)."',
email_message='".$db_link->escape_string($email_message)."',
is_disable=".$is_disable.",
need_attachments=".$need_attachments.",

sortorder=".$sortorder.",
other_fields='".$db_link->escape_string($other_fields)."',
attachments='".$db_link->escape_string($attachments)."',
localization_set_id=".$localization_set_id.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_email_template = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql="delete from gks_email_template_object_forms where email_template_id=".$id;
if (count($fobjects_ids)>0) $sql.=" and email_template_object_id not in (".implode(',',$fobjects_ids).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }


foreach ($fobjects_ids as $value) {
  $sql="select * from gks_email_template_object_forms where email_template_id=".$id." and email_template_object_id=".$value;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_email_template_object_forms (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      email_template_id,email_template_object_id
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


//echo '<pre>';echo $localization_set_id; die();
if ($localization_set_id>0 and count($loc_langs_array_clean)>0) {
  $sql="update gks_email_template set localization_set_id=".$localization_set_id."
  where id_email_template in (".implode(',',$loc_langs_array_clean).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect,'preview_url' => '');
echo json_encode($return); die();







