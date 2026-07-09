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
$my_page_title=gks_lang('Αποθήκευση custom table').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_custom_table',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





if ($id>0) {
  $sql ="SELECT * FROM gks_custom_table 
  where ((custom_table_disabled=0 and id_custom_table<10000) or (id_custom_table>=10000))
  and id_custom_table = ".$id;
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
  $row = $result->fetch_assoc();
  $custom_table_name=$row['custom_table_name'];
}

$custom_table_descr=''; if (isset($_POST['custom_table_descr'])) $custom_table_descr=trim_gks(base64_decode($_POST['custom_table_descr']));
if ($id==-1 or $id>=10000) {
  if ($custom_table_descr=='') {debug_mail(false,'emptyl',         gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
    echo json_encode($return); die(); }
    
  $sql="select * from gks_custom_table where custom_table_descr like '".$db_link->escape_string($custom_table_descr)."' and id_custom_table<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-custom-item.php?id=[2]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',$custom_table_descr,$message);
    $message=str_replace('[2]',$row['id_custom_table'],$message);    
    debug_mail(false,'custom_table exist',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}
$custom_table_disabled=0;if (isset($_POST['custom_table_disabled'])) $custom_table_disabled=intval($_POST['custom_table_disabled']);
$num_columns=0;if (isset($_POST['num_columns'])) $num_columns=intval($_POST['num_columns']);
if (in_array($num_columns,[1,2,3,4,6])==false) $num_columns=1;

if ($id==-1 or $id>=10000) {
  $erp_app_id_check=0; if (isset($_POST['erp_app_id_check'])) $erp_app_id_check=intval($_POST['erp_app_id_check']);
  $erp_app_filter_val_webpage_computer=0; if (isset($_POST['erp_app_filter_val_webpage_computer'])) $erp_app_filter_val_webpage_computer=intval($_POST['erp_app_filter_val_webpage_computer']);
  $erp_app_filter_val_webpage_tablet=0; if (isset($_POST['erp_app_filter_val_webpage_tablet'])) $erp_app_filter_val_webpage_tablet=intval($_POST['erp_app_filter_val_webpage_tablet']);
  $erp_app_filter_val_webpage_mobile=0; if (isset($_POST['erp_app_filter_val_webpage_mobile'])) $erp_app_filter_val_webpage_mobile=intval($_POST['erp_app_filter_val_webpage_mobile']);
  $erp_app_filter_val_app_with_thermal=0; if (isset($_POST['erp_app_filter_val_app_with_thermal'])) $erp_app_filter_val_app_with_thermal=intval($_POST['erp_app_filter_val_app_with_thermal']);
  $erp_app_filter_val_app_no_thermal=0; if (isset($_POST['erp_app_filter_val_app_no_thermal'])) $erp_app_filter_val_app_no_thermal=intval($_POST['erp_app_filter_val_app_no_thermal']);
  $erp_app_id=0; if (isset($_POST['erp_app_id'])) $erp_app_id=intval($_POST['erp_app_id']);
  $erp_app_dest=''; if (isset($_POST['erp_app_dest'])) $erp_app_dest=trim_gks(base64_decode($_POST['erp_app_dest']));
  $erp_app_dest_printer=''; if (isset($_POST['erp_app_dest_printer'])) $erp_app_dest_printer=trim_gks(base64_decode($_POST['erp_app_dest_printer']));
  $erp_app_dest_printer_method=0; if (isset($_POST['erp_app_dest_printer_method'])) $erp_app_dest_printer_method=intval($_POST['erp_app_dest_printer_method']);
  $erp_app_dest_printer_lpr_ip=''; if (isset($_POST['erp_app_dest_printer_lpr_ip'])) $erp_app_dest_printer_lpr_ip=trim_gks(base64_decode($_POST['erp_app_dest_printer_lpr_ip']));
  $erp_app_dest_printer_copies=0; if (isset($_POST['erp_app_dest_printer_copies'])) $erp_app_dest_printer_copies=intval($_POST['erp_app_dest_printer_copies']);
  $erp_app_dest_folder=''; if (isset($_POST['erp_app_dest_folder'])) $erp_app_dest_folder=trim_gks(base64_decode($_POST['erp_app_dest_folder']));
  
  
  if ($erp_app_id_check!=0) $erp_app_id_check=1;
  if ($erp_app_id_check==0) {
    $erp_app_id=0;  
    $erp_app_dest='';
    $erp_app_dest_printer='';
    $erp_app_dest_printer_method=0;
    $erp_app_dest_printer_lpr_ip='';
    $erp_app_dest_printer_copies=0;
    $erp_app_dest_folder='';
    $erp_app_filter='';
  } else {
    if ($erp_app_id<1) {
      debug_mail(false,'erp_app_id is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την εφαρμογή gks ERP App Desktop')));
      echo json_encode($return); die(); } 
    
    $erp_app_filter=[];
    if ($erp_app_filter_val_webpage_computer) $erp_app_filter[]='webpage_computer';
    if ($erp_app_filter_val_webpage_tablet) $erp_app_filter[]='webpage_tablet';
    if ($erp_app_filter_val_webpage_mobile) $erp_app_filter[]='webpage_mobile';
    if ($erp_app_filter_val_app_with_thermal) $erp_app_filter[]='app_with_thermal';
    if ($erp_app_filter_val_app_no_thermal) $erp_app_filter[]='app_no_thermal';
    if (count($erp_app_filter)==0) {
      debug_mail(false,'erp_app_filter is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τουλάχιστον ένα φίλτρο στο gks ERP App Desktop')));
      echo json_encode($return); die(); }    
    
    $erp_app_filter=json_encode($erp_app_filter);
    
    
    $sql="select * from gks_erp_app where id_erp_app=".$erp_app_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows<=0) {
      debug_mail(false,'erp_app_id not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η gks ERP App Desktop')));
      echo json_encode($return); die(); } 
  
    if ($erp_app_dest!='printer' and $erp_app_dest!='folder') $erp_app_dest='';
    if ($erp_app_dest=='') {
      debug_mail(false,'erp_app_dest is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Προορισμός')));
      echo json_encode($return); die(); }
    
    if ($erp_app_dest=='printer') {
      $erp_app_dest_folder='';
      
      if ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1) $erp_app_dest_printer_lpr_ip='';
      if ($erp_app_dest_printer_method==2) $erp_app_dest_printer='';
      if ($erp_app_dest_printer_method==3) {$erp_app_dest_printer_lpr_ip=''; $erp_app_dest_printer=''; }
      
  
      if ($erp_app_dest_printer_method < 0 or $erp_app_dest_printer_method > 3) {
        debug_mail(false,'erp_app_dest_printer_method is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Η μέθοδος πρέπει να είναι 0,1,2 ή 3')));
        echo json_encode($return); die(); } 
      
      if ($erp_app_dest_printer=='' and ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1)) {
        debug_mail(false,'erp_app_dest_printer is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Εκτυπωτή')));
        echo json_encode($return); die(); } 
      if ($erp_app_dest_printer_lpr_ip=='' and $erp_app_dest_printer_method==2) {
        debug_mail(false,'erp_app_dest_printer_lpr_ip is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε την IP του εκτυπωτή')));
        echo json_encode($return); die(); } 
        
        
      if ($erp_app_dest_printer_copies < 1 and $erp_app_dest_printer_copies > 5) {
        debug_mail(false,'erp_app_dest_printer_copies is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Τα αντίτυπα πρέπει να είναι 1,2,3,4 ή 5')));
        echo json_encode($return); die(); } 
      
      //echo '<pre>'. $erp_app_dest_printer;die();    
      
    } else if ($erp_app_dest=='folder') {
      $erp_app_dest_printer='';
      $erp_app_dest_printer_method=0;
      $erp_app_dest_printer_lpr_ip='';
      $erp_app_dest_printer_copies=0;
      
      if ($erp_app_dest_folder=='') {
        debug_mail(false,'erp_app_dest_folder is empty','');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον φάκελο αποστολής')));
        echo json_encode($return); die(); } 
      
      if (endwith($erp_app_dest_folder,'\\')==false) $erp_app_dest_folder.='\\';
      
      $params=array(
        'id' => $erp_app_id,
        'cmd' => 'run_command_folder_exist',
        'postdata' => array (
          'folder' => $erp_app_dest_folder,
          'and_writable' => true,
        ),
      );
      $gks_erp_run_result=gks_erp_app_run_command($params);
  
      if ($gks_erp_run_result['success']==false) {
        $return = array('success' => false, 'message' => base64_encode($gks_erp_run_result['message']));
        echo json_encode($return); die(); }
      
      
  
              
      //print '<pre>wwwwwwwwwwwww';print_r($gks_erp_run_result);die();
      
    }
    
  }
}

$mysort_str = trim_gks(base64_decode($_POST['mysort_str']));
$mysort = json_decode($mysort_str, true);
if ($mysort === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mysort_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

//print '<pre>';print_r($mysort);die();
foreach ($mysort as $index => $value) {
  if (isset($value['card_name'])==false or trim_gks($value['card_name'])=='') {
    debug_mail(false,'type_id not in list',print_r($mysort,true));
    $message=gks_lang('Στην καρτέλα <b>[1]</b> δεν έχετε ορίσει την περιγραφή');
    $message=str_replace('[1]',($index+1),$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}    
}
$unique_names=[];
foreach ($mysort as $value) {
  $temp=trim_gks($value['card_name']);
  if (in_array($temp,$unique_names)) {
    $message=gks_lang('Το όνομα καρτέλας <b>[1]</b> υπάρχει πάνω από μία φορά');
    $message=str_replace('[1]',$temp,$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();   
  }
  $unique_names[]=$temp;
}


$card_name_settings=[];
foreach ($mysort as $value) {
  $card_name_settings[]=array(
    'name'=>trim_gks($value['card_name']),
    'sortorder'=>intval($value['card_sortorder']),
    'width'=>intval($value['card_width']),
  );
}

function mysort_card_name_array($a, $b) {
  if ($a['sortorder'] > $b['sortorder']) return 1;
  if ($a['sortorder'] < $b['sortorder']) return -1;
  return 0;
}
usort($card_name_settings, "mysort_card_name_array");
//echo '<pre>';print_r($card_name_settings);print_r($mysort);die();

$unique_names=[];$cc=0;
foreach ($card_name_settings as &$value) {
  $cc++;
  $value['dbname']=str_pad($cc, 8, '0', STR_PAD_LEFT);
  $unique_names[$value['name']]=$value['dbname'];
}
unset($value);
//echo '<pre>';print_r($unique_names);print_r($card_name_settings);die();



$myfields_str = trim_gks(base64_decode($_POST['myfields_str']));
$myfields = json_decode($myfields_str, true);
if ($myfields === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['myfields_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

$sql="select id_custom_field_type from gks_custom_field_type where field_type_notdevyet=0";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$types_id=array();
while ($row = $result->fetch_assoc()) {
  $types_id[]=$row['id_custom_field_type'];
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($types_id,true)));
//echo json_encode($return); die();

foreach($myfields as &$myf) {
  $myf['sortorder']=1000;
  $myf['card_name']='';
}
unset($myf);

foreach($myfields as &$myf) {
  $found=false;
  foreach ($mysort as $mycard) {
    foreach ($mycard['myar'] as $myi => $mys) {
      if ($mys==$myf['aa']) {
        $found=true;
        $myf['sortorder']=$myi;
        $myf['card_name']=trim_gks($mycard['card_name']);
        if (isset($unique_names[$myf['card_name']])) {
          $myf['card_name']=$unique_names[$myf['card_name']];
        }
        break;
      }
    }
    if ($found) break;
  } 
}
unset($myf);
//echo '<pre>';print_r($myfields);die();


$all_labels=array();
foreach($myfields as &$myf) {
  $myf['recid']=intval($myf['recid']);
  if ($myf['recid']<0) $myf['recid']=0;
  
  $myf['label']=trim_gks($myf['label']);
  if ($myf['label']=='') {
    debug_mail(false,'type_id not in list',print_r($myf,true));
    $message=gks_lang('Ορίστε το όνομα του πεδίου με aa <b>[1]</b>');
    $message=str_replace('[1]',$myf['aa'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  if (in_array($myf['label'], $all_labels)) {
    debug_mail(false,'type_id not in list',print_r($myf,true));
    $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει σε περισσότερα από ένα πεδία');
    $message=str_replace('[1]',$myf['label'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $all_labels[]=$myf['label'];
    
  $myf['type_id']=intval($myf['type_id']);
  if (in_array($myf['type_id'],$types_id)==false) {
    debug_mail(false,'type_id not in list',print_r($myf,true));
    $message=gks_lang('Ορίστε τον τύπο του πεδίου με όνομα <b>[1]</b>');
    $message=str_replace('[1]',$myf['label'],$message);    
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
    
  if ($myf['allow_null']!=1) $myf['allow_null']=0;
  if ($myf['show_on_list']!=1) $myf['show_on_list']=0;
  
  $myf['default_value']=trim_gks($myf['default_value']);
  
  $myf['field_attr']='';
  
  if ($myf['type_id']==501) { //501 epilogi enos apo lista
    if (isset($myf['options'])==false or is_array($myf['options']) == false or count($myf['options'])<=0) {
      debug_mail(false,'options not set',print_r($myf,true));
      $message=gks_lang('Ορίστε τις επιλογές για το πεδίο με όνομα <b>[1]</b>');
      $message=str_replace('[1]',$myf['label'],$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();}      
    
    $field_attr=array();
    $field_attr['options']=array();
    $options_vals=array();
    $options_texts=array();
    
    foreach ($myf['options'] as $index => $myo) {
      $oval=0;
      $otext='';
      if (isset($myo['value'])) {
        $oval=intval($myo['value']);
      }
      if (isset($myo['text'])) {
        $otext=trim_gks($myo['text']);
      }
      if ($oval<=0) {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Ορίστε μία θετική τιμή στην επιλογή <b>[1]</b> στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',($index+1),$message);
        $message=str_replace('[2]',$myf['label'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
      if ($otext=='') {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Ορίστε μία περιγραφή στην επιλογή <b>[1]</b> στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',($index+1),$message);
        $message=str_replace('[2]',$myf['label'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
        
      if (in_array($oval,$options_vals)) {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Η τιμή <b>[1]</b> υπάρχει πάνω από μία φορά στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',$oval,$message);
        $message=str_replace('[2]',$myf['label'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
      $options_vals[]=$oval;
      
      if (in_array($otext,$options_texts)) {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Η περιγραφή <b>[1]</b> υπάρχει πάνω από μία φορά στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',$otext,$message);
        $message=str_replace('[2]',$myf['label'],$message);        
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
      $options_texts[]=$otext;
      
      $field_attr['options'][]=array('value' => $oval, 'text' => $otext);
    } 
    //array('value' => 1,'text'=> 'epilogi 1'),
    //print '<pre>';print_r($myf['options']);die();
    $myf['field_attr']=serialize($field_attr);
    //print '<pre>';print $myf['field_attr'];die();
      
    
  } else if ($myf['type_id']==502) { //502 epilogi pollon apo lista
    if (isset($myf['options'])==false or is_array($myf['options']) == false or count($myf['options'])<=0) {
      debug_mail(false,'options not set',print_r($myf,true));
      $message=gks_lang('Ορίστε τις επιλογές για το πεδίο με όνομα <b>[1]</b>');
      $message=str_replace('[1]',$myf['label'],$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();}      

    $field_attr=array();
    $field_attr['options']=array();
    $options_texts=array();
    foreach ($myf['options'] as $index => $myo) {
      $otext=trim_gks($myo);      
      if ($otext=='') {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Ορίστε μία περιγραφή στην επιλογή <b>[1]</b> στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',($index+1),$message);
        $message=str_replace('[2]',$myf['label'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
        
      if (in_array($otext,$options_texts)) {
        debug_mail(false,'options not set',print_r($myf,true));
        $message=gks_lang('Η περιγραφή <b>[1]</b> υπάρχει πάνω από μία φορά στο πεδίο <b>[2]</b>');
        $message=str_replace('[1]',$otext,$message);
        $message=str_replace('[2]',$myf['label'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die();}      
      $options_texts[]=$otext;
      
      $field_attr['options'][]=$otext;
    }     
    //array('value' => 1,'text'=> 'epilogi 1'),
    //print '<pre>';print_r($myf['options']);die();
    $myf['field_attr']=serialize($field_attr);
    //print '<pre>';print $myf['field_attr'];die();
    
  }
}
unset($myf);

if (($id==-1 or $id>=10000) and count($myfields)==0) {
  debug_mail(false,gks_lang('Προσθέστε κάποια πεδία'),'<pre>'.print_r($mysort,true).print_r($myfields,true).'</pre>');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Προσθέστε κάποια πεδία')));
  echo json_encode($return); die();}

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($mysort,true).print_r($myfields,true)));
//echo json_encode($return); die();


$redirect='';
if ($id==-1) {
  $sql="ALTER TABLE gks_custom_table AUTO_INCREMENT = 10001;";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  
  $sql="insert into gks_custom_table (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $custom_table_name='gks_ct_'.$id;
  $field_name_id_parent='id_gks_customt_gks_ct_'.$id;
  $field_name_id_current='ct_'.$id.'_id';
  
  $shortcode_prefix=gks_shortcode_prefix_for_custom_table();
  
  
  
  $sql="update gks_custom_table set 
  custom_table_name='".$custom_table_name."',
  field_name_id_parent='".$field_name_id_parent."',
  field_name_id_current='".$field_name_id_current."',
  custom_sortorder=".$id.",
  obj_url='admin-ct.php?ctid=".$id."',
  custom_priv='ct',
  shortcode_prefix='".$db_link->escape_string($shortcode_prefix)."'
  where id_custom_table=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  

  $sql="insert into gks_print_objects (
  id_print_object,mydate_add, mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_descr
  ) values (
  ".$id.",now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  '".$custom_table_name."',
  '".$db_link->escape_string($custom_table_descr)."'
  )";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  
  $redirect=base64_encode('admin-custom-item.php?id='.$id); 
  
}




$sql="update gks_custom_field set field_disabled=1 where custom_table_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

foreach($myfields as &$myf) {

  if ($myf['card_name']==gks_lang('Προσαρμοσμένα')) $myf['card_name']='';
  
  if ($myf['recid']>0) { //update
    $sql="update gks_custom_field set 
    field_label='".$db_link->escape_string($myf['label'])."',
    field_disabled=0,
    field_type_id=".$myf['type_id'].",
    field_allow_null=".$myf['allow_null'].",
    field_default_value='".$db_link->escape_string($myf['default_value'])."',
    field_attr='".$db_link->escape_string($myf['field_attr'])."',
    field_sortorder=".$myf['sortorder'].",
    field_card_name='".$db_link->escape_string($myf['card_name'])."',
    field_show_on_list=".$myf['show_on_list'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_custom_field=".$myf['recid']." and custom_table_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    
  } else { //insert
    $sql="insert into gks_custom_field (
    custom_table_id,
    field_label,
    field_disabled,
    field_type_id,
    field_allow_null,
    field_default_value,
    field_attr,
    field_sortorder,
    field_card_name,
    field_show_on_list,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
    ".$id.",
    '".$db_link->escape_string($myf['label'])."',
    0,
    ".$myf['type_id'].",
    ".$myf['allow_null'].",
    '".$db_link->escape_string($myf['default_value'])."',
    '".$db_link->escape_string($myf['field_attr'])."',
    ".$myf['sortorder'].",
    '".$db_link->escape_string($myf['card_name'])."',
    ".$myf['show_on_list'].",
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
        
  }
}
unset($myf);

$sql ="update gks_custom_table set
card_name_settings='".$db_link->escape_string(json_encode($card_name_settings))."'";

if ($id>=10000) {
  $sql.=",
  custom_table_descr='".$db_link->escape_string($custom_table_descr)."',
  custom_table_disabled=".$custom_table_disabled.",
  num_columns=".$num_columns.",
  erp_app_id=".$erp_app_id.",
  erp_app_filter='".$db_link->escape_string($erp_app_filter)."',
  erp_app_dest='".$db_link->escape_string($erp_app_dest)."',
  erp_app_dest_printer='".$db_link->escape_string($erp_app_dest_printer)."',
  erp_app_dest_printer_method=".$erp_app_dest_printer_method.",
  erp_app_dest_printer_lpr_ip='".$db_link->escape_string($erp_app_dest_printer_lpr_ip)."',
  erp_app_dest_printer_copies=".$erp_app_dest_printer_copies.",
  erp_app_dest_folder='".$db_link->escape_string($erp_app_dest_folder)."'";
}
$sql.=" where id_custom_table = ".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}  

if ($id>=10000) {
  
  $sql ="update gks_print_objects set 
  object_descr='".$db_link->escape_string($custom_table_descr)."'
  where id_print_object=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $sql ="update gks_email_template_object set 
  object_descr='".$db_link->escape_string($custom_table_descr)."'
  where id_email_template_object=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  

  $sql ="update gks_sms_viber_template_object set 
  object_descr='".$db_link->escape_string($custom_table_descr)."'
  where id_sms_viber_template_object=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
}


$ret=gks_custom_table_db_all_create();
if ($ret['success']==false) {
      debug_mail(false,'error gks_custom_table_db_all_create',print_r($ret,true));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Εσωτερικό σφάλμα').' 12452363345<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
      echo json_encode($return); die();}

$ret = gks_custom_table_db_update($custom_table_name);
if ($ret['success']==false) {
      debug_mail(false,'error gks_custom_table_db_update',print_r($ret,true));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Εσωτερικό σφάλμα').' 12452363346<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));
      echo json_encode($return); die();}


$sql ="update gks_custom_table set
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_custom_table = ".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();



$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($mysort,true).print_r($myfields,true)));
echo json_encode($return); die();

