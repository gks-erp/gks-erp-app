<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();
if ($my_wp_user_id <= 0) {
  die();
}



$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}

$my_page_title=gks_lang('Ορισμός φωτογραφίας για την εκτύπωση σε εγγραφή');
db_open();
stat_record();

$data_path=''; if (isset($_POST['data_path'])) $data_path=trim_gks(base64_decode($_POST['data_path']));
$object_name=''; if (isset($_POST['object_name'])) $object_name=trim_gks(base64_decode($_POST['object_name']));
$field='show_print'; if (isset($_POST['field'])) $field=trim_gks($_POST['field']);
//show_print or descr or expire_date
if ($field=='show_print') {
  $data_value=0; if (isset($_POST['data_value'])) $data_value=intval($_POST['data_value']);
  if ($data_value==0) $data_value=1; else $data_value=0; //inverse value
} else if ($field=='descr') {
  $data_value=''; if (isset($_POST['data_value'])) $data_value=trim_gks(base64_decode($_POST['data_value']));

} else if ($field=='expire_date') {
  $data_value='';
  if ($_POST['data_value'] == '__/__/____ __:__') $_POST['data_value']='';
  if ($_POST['data_value']!='') {
    //echo '<pre>'.$_POST['data_value']; die();
    $data_value=trim_gks($_POST['data_value']);
    if ($data_value!='') $data_value = mystrtodb($data_value);
  }
}

//echo '<pre>'.$data_value; die();

$object_map=gks_FilesObjectList_map($object_name);
$object_path=$object_map['path'];
$object_table=$object_map['table'];
$object_tid=$object_map['tid'];
$object_pid=$object_map['pid'];


$public_shortcode='';
$public_myopencount=0;
$sql="select * from ".$object_table." where ".$object_pid."=".$id." and photo_url like '".$db_link->escape_string($data_path)."' order by ".$object_tid." desc limit 1";
//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}

if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $public_shortcode=trim($row['public_shortcode']);
  $public_myopencount=intval($row['public_myopencount']);
  
  $sql="update ".$object_table." set ";
  if ($field=='show_print') {
    $sql.="show_print=".$data_value." ";
  } else if ($field=='descr') {
    $sql.="descr='".$db_link->escape_string($data_value)."' ";
  } else if ($field=='expire_date') {
    $public_expire_date=$data_value;
    if ($data_value=='') {
      $sql.="public_expire_date=null ";
    } else {
      $sql.="public_expire_date='".$db_link->escape_string($data_value)."' ";
    }
  }
  $sql.="where ".$object_tid."=".$row[$object_tid];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
} else {
  $mysize=0;
  $file_path=GKS_FileServerShare.$object_path.$id.'/'.$data_path;
  if (file_exists($file_path)) {
    $mysize=filesize($file_path);
  }
  
  $show_print=0;
  $descr="''";
  $public_expire_date='';
  
  if ($field=='show_print') {
    $show_print=$data_value;
  } else if ($field=='descr') {
    $descr="'".$db_link->escape_string($data_value)."'";
  } else if ($field=='expire_date') {
    $public_expire_date=$data_value;
  }
  
  $sql="insert into ".$object_table." (
    mydate,ip,".$object_pid.",photo_url,mysize,user_add_id,
    filesobjectlist,
    show_print,descr,public_expire_date
  ) values (
    NOW(),
    '".$db_link->escape_string($gkIP)."',
    ".$id.",
    '".$db_link->escape_string($data_path)."',
    ".$mysize.",
    ".$my_wp_user_id.",
    1,
    ".$show_print.",
    ".$descr.",
    ".($public_expire_date=='' ? 'null' : "'".$db_link->escape_string($public_expire_date)."'")."
  )";
  //echo '<pre>'.$sql;die();
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}   
}


if ($field=='expire_date') {
  
  $gks_FilesObjectList_shortcode_prefix=$object_map['shortcode_prefix'];
  $action='';
  $public_shortcode_full='';
  if($public_expire_date!='' and $public_shortcode=='') {
    $ret=gks_fileserver_item_create_public_shortcode($object_name,$data_path);
    $public_shortcode=$ret['code'];
    $action=$ret['action'];
    $public_shortcode_full=$ret['full'];
  } else if ($public_shortcode!='') {
    $public_shortcode_full=GKS_SITE_URL.'s/'.$gks_FilesObjectList_shortcode_prefix.$public_shortcode;
  }
  
  
  $input=[];
  $input[$data_path]=[];
  $input[$data_path]['public_expire_date']=$public_expire_date;
  $input[$data_path]['public_shortcode']=$public_shortcode;
  $input[$data_path]['public_myopencount']=$public_myopencount;
  
  $data_value=gks_fileserver_item_render_public_expire_date($input,$data_path);
  
  $return = array(
    'success' => true, 
    'message' => base64_encode('OK'), 
    'data_path'=> $data_path, 
    'data_value' => $data_value,
    'action' => $action,
    'public_shortcode_full' => $public_shortcode_full,
    'public_shortcode'=>$public_shortcode,
    'public_shortcode_ws'=>$gks_FilesObjectList_shortcode_prefix.$public_shortcode,
  );  
  echo json_encode($return); die();  

} else {
  $return = array(
    'success' => true, 
    'message' => base64_encode('OK'), 
    'data_path'=> $data_path, 
    'data_value' => $data_value,
  );
  echo json_encode($return); die();  
}

