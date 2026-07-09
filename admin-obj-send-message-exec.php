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
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$page='';if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page'])); //p.x. /my/admin-orders-item.php
if ($page!='') {
  $parts=explode('/',$page);
  $page=$parts[count($parts)-1]; //p.x. admin-orders-item.php
}
$model='';$human_text='';$parent_table_name='';$table_name='';$field_id='';$field_pid='';$field_text='';
if ($page=='admin-orders-item.php')       {$model='order';   $human_text=gks_lang('παραγγελία'); $parent_table_name='gks_orders';  $table_name='gks_orders_messages';  $field_id='id_order_message';   $field_pid='order_id';   $field_text='order_message';}
else if ($page=='admin-acc-inv-item.php') {$model='acc_inv'; $human_text=gks_lang('παραστατικό');$parent_table_name='gks_acc_inv'; $table_name='gks_acc_inv_messages'; $field_id='id_acc_inv_message'; $field_pid='acc_inv_id'; $field_text='acc_inv_message';}
else if ($page=='admin-acc-pay-item.php') {$model='acc_pay'; $human_text=gks_lang('πληρωμή');    $parent_table_name='gks_acc_pay'; $table_name='gks_acc_pay_messages'; $field_id='id_acc_pay_message'; $field_pid='acc_pay_id'; $field_text='acc_pay_message';}
else if ($page=='admin-whi-mov-item.php') {$model='whi_mov'; $human_text=gks_lang('δελτίο');     $parent_table_name='gks_whi_mov'; $table_name='gks_whi_mov_messages'; $field_id='id_whi_mov_message'; $field_pid='whi_mov_id'; $field_text='whi_mov_message';}
else if ($page=='admin-hotel-reservation-item.php') {$model='hotel-reservation'; $human_text=gks_lang('κράτηση');     $parent_table_name='gks_hotel_reservation'; $table_name='gks_hotel_reservation_messages'; $field_id='id_hotel_reservation_message'; $field_pid='hotel_reservation_id'; $field_text='hotel_reservation_message';}
else if ($page=='admin-transfer-reservation-item.php') {$model='transfer-reservation'; $human_text=gks_lang('κράτηση');     $parent_table_name='gks_transfer_reservation'; $table_name='gks_transfer_reservation_messages'; $field_id='id_transfer_reservation_message'; $field_pid='transfer_reservation_id'; $field_text='transfer_reservation_message';}
else if ($page=='admin-crm-task-item.php') {$model='crm_tasks'; $human_text=gks_lang('εργασία');$parent_table_name='gks_crm_tasks'; $table_name='gks_crm_tasks_messages'; $field_id='id_crm_tasks_message'; $field_pid='crm_tasks_id'; $field_text='crm_tasks_message';}
else if ($page=='admin-crm-lead-item.php') {$model='crm_leads'; $human_text=gks_lang('ευκαιρία');$parent_table_name='gks_crm_leads'; $table_name='gks_crm_leads_messages'; $field_id='id_crm_leads_message'; $field_pid='crm_leads_id'; $field_text='crm_leads_message';}
else if ($page=='admin-crm-machine-item.php') {$model='crm_machine'; $human_text=gks_lang('συσκευή');$parent_table_name='gks_crm_machine'; $table_name='gks_crm_machine_messages'; $field_id='id_crm_machine_message'; $field_pid='crm_machine_id'; $field_text='crm_machine_message';}
else if ($page=='admin-users-item.php') {$model='users'; $human_text=gks_lang('επαφή');$parent_table_name='wp_users'; $table_name='gks_users_messages'; $field_id='id_users_message'; $field_pid='userfor_id'; $field_text='userfor_message';}
else if ($page=='admin-ct-item.php') {
  $model='customt'; 
  $human_text=gks_lang('Δικά μου αντικείμενα');
  $parent_table_name='wp_users'; 
  $table_name='gks_users_messages'; 
  $field_id='id_users_message'; 
  $field_pid='userfor_id'; 
  $field_text='userfor_message';
}



if ($model=='') {
  debug_mail(false,'the model is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το μοντέλο')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη μηνύματος σε').' '.$human_text;
db_open();
stat_record();

if ($model=='customt') {
  $ctid=0;if (isset($_POST['ctid'])) $ctid=intval($_POST['ctid']);
  if ($ctid <= 0) {
    debug_mail(false,'the ctid is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ctid'));
    echo json_encode($return); die();}
  
  $sql_ct="select * 
  from gks_custom_table 
  where custom_table_disabled=0
  and id_custom_table=".$ctid;
  $result_ct = $db_link->query($sql_ct);        
  if (!$result_ct) {
    debug_mail(false,'error sql',$sql_ct);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_ct->num_rows!=1) {
    debug_mail(false,'error sql',$sql_ct);
    $return = array('success' => false, 'message' => base64_encode('custom table not found ('.$ctid.')'));
    echo json_encode($return); die();}
  $row_ct = $result_ct->fetch_assoc();
  $custom_table_descr=$row_ct['custom_table_descr'];
  $custom_table_name=$row_ct['custom_table_name'];
  $custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
  $field_name_id_parent=$row_ct['field_name_id_parent'];
  $field_name_id_current=$row_ct['field_name_id_current'];
  //$field_id='id_gks_customt_gks_ct_'.$ctid;  

  $model=$custom_table_name; 
  $parent_table_name=$custom_table_name;  
  $table_name=$custom_table_name_real.'_messages';  
  $field_id=$field_name_id_parent.'_message';   
  $field_pid='gks_customt_gks_'.$field_name_id_current;   
  $field_text='customt_message';

  //echo '<pre>';print_r($row_ct);die();
  
}
 
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $parent_table_name,'edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$ispreview=false; if (isset($_POST['ispreview']) and $_POST['ispreview']=='1') $ispreview=true;
$order_online=false; if (isset($_POST['order_online']) and $_POST['order_online']=='1') $order_online=true;
$online_url='';if (isset($_POST['online_url'])) $online_url=trim_gks(base64_decode($_POST['online_url']));
//echo '<pre>aaaa '.$online_url;die();

$typeid=0;
if (isset($_POST['typeid'])) $typeid=intval($_POST['typeid']);
if ($typeid!=1 and $typeid!=2 and $typeid!=3 and $typeid!=4) {
  debug_mail(false,'the typeid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο τύπος του μηνύματος')));
  echo json_encode($return); die();}

//if ($typeid==3) {
//  debug_mail(false,'not yet enable','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτός ο τρόπος δεν έχει ακόμη ενεργοποιηθεί')));
//  echo json_encode($return); die();}
  

$message='';if (isset($_POST['message'])) $message=base64_decode($_POST['message']);
$message_order_online=$message;

if ($ispreview==false and $message=='') {
  debug_mail(false,'the message is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το κείμενο του μηνύματος')));
  echo json_encode($return); die();}




if ($typeid==2) { //email
  if (strpos($message, '<br />')===false and 
      strpos($message, '<br/>')===false and 
      strpos($message, '<br>')===false and 
      strpos($message, '<p>')===false and 
      strpos($message, '<p ')===false and 
      strpos($message, '<div>')===false and 
      strpos($message, '<div ')===false and 
      strpos($message, '<ul>')===false and 
      strpos($message, '<li>')===false and 
      strpos($message, '<img ')===false) {
   $message=nl2br_gks($message);
  }
  
  $template=0;if (isset($_POST['template'])) $template=intval($_POST['template']);
  if ($template<=0) {
    debug_mail(false,'the template is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε το πρότυπο email')));
    echo json_encode($return); die();}
    
  $sql="select * from gks_email_template where id_email_template=".$template." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  if ($result->num_rows!=1) {
    debug_mail(false,'the template is not found','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πρότυπο email').' <b>'.$template.'</b>'));
    echo json_encode($return); die();}    

  $row_template = $result->fetch_assoc();
  $need_attachments=intval($row_template['need_attachments']);
  //echo '<pre>';print_r($row_template);die();
  
  $subject='';if (isset($_POST['subject'])) $subject=trim_gks(base64_decode($_POST['subject']));
  if ($ispreview==false and $subject=='') {
    debug_mail(false,'the subject is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το θέμα του email')));
    echo json_encode($return); die();}

  $email_from='';if (isset($_POST['email_from'])) $email_from=trim_gks(base64_decode($_POST['email_from']));
  if ($ispreview==false and $email_from=='') {
    debug_mail(false,'the email_from is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το email σας στο πεδίο <b>Από</b>')));
    echo json_encode($return); die();}
  
  $email_to='';if (isset($_POST['email_to'])) $email_to=trim_gks(base64_decode($_POST['email_to']));
  if ($ispreview==false and $email_to=='') {
    debug_mail(false,'the email_to is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το email του αποδέκτη στο πεδίο <b>Προς</b>')));
    echo json_encode($return); die();}
    
  
  $replaces=array();
  foreach ($_POST as $key => $value) {
    if (substr($key,0,12)=='email_param_') {
      $replaces[] = array('[['.substr($key,12).']]', trim_gks(base64_decode($value)));    
    }
  }
  foreach ($replaces as $repval) {
    $subject=str_replace($repval[0],$repval[1], $subject);
    $message = str_replace($repval[0],$repval[1], $message);
  }
  $replaces[] = array('[[message]]', $message);

  $message_order_online=$message;
  

  $list_files_str = trim_gks(base64_decode($_POST['list_files_str']));
  //$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 
  
  $list_files = json_decode($list_files_str, true);
  if ($list_files === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['list_files_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}

  //print '<pre>';print_r($list_files);die();
  
  $Attachments=array();
  foreach ($list_files as $file_value) {
    $mybasefolder=trim_gks($file_value['basefolder']);
    
    $afile=trim_gks($file_value['path']);
    if ($afile!='') {
    	if ($model=='crm_tasks' and $afile=='invite.ics') {
    		
    		$dir_path=GKS_FileServerShare.'crm/task/'.$id.'/'; 
    		$file_path=$dir_path.$afile;
    		if (file_exists($file_path)) {
    			@unlink($file_path);
    		}
    		if (file_exists($dir_path) == false) {
			    if (@mkdir($dir_path , 0755, true) == false ) {
			      debug_mail(false,'can not create dir: ',$dir_path);
			      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '. $dir_path));
			      echo json_encode($return); die();
			    }
			  }
    		
 
    		$ret = gks_crm_tasks_create_rantevou_ics_file($id,$file_path,$email_to);
				if ($ret==false) {
		      debug_mail(false,'can not create ics file: ',$file_path);
		      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το αρχείο ραντεβού').': '. $file_path));
		      echo json_encode($return); die();}
				
				
    		//print '<pre>';print $file_path.' | '.$afile;die();
    		$Attachments[]=array($file_path,$afile);
     	} else {

        //$afile2=GKS_FileServerShare.$afile;
        
        if ($mybasefolder=='erplo') $afile2=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site/'.$afile;
        else if ($mybasefolder=='erpfi') $afile2=GKS_FileServerShare.$afile;
        else if ($mybasefolder=='erpul') $afile2=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/'.$afile;
        else if ($mybasefolder=='erpdl') $afile2=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install/'.$afile;
        else if ($mybasefolder=='wodpr') $afile2=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/uploads/'.$afile;
	      
	      if (file_exists($afile2)==false) {
	        debug_mail(false,'json_decode error',$_POST['list_files_str']);
	        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το αρχείο').': '. mb_basename($afile)));
	        echo json_encode($return); die();}
	      $Attachments[]=array($afile2,mb_basename($afile));
	      //print '<pre>';print $afile2.' | '.mb_basename($afile);die();
      }
    }
  } 
  //print '<pre>';print_r($Attachments);die();
  
  if ($need_attachments!=0 and count($Attachments)==0) {
    debug_mail(false,'json_decode error',$_POST['list_files_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Για αυτό το πρότυπο email απαιτείται τουλάχιστον ένα συνημμένο')));
    echo json_encode($return); die();}
  
  if ($ispreview) {
    $params=array(
      'model'=>$model,
      'model_id'=>$id,
      'from' => $email_from,
      'to'=>$email_to,
      'subject'=>$subject,
      'template'=>$template,
      'replaces'=>$replaces,
      'Attachments'=>$Attachments,
      'ispreview'=>$ispreview,
    );
    $res=gks_mymail_template($params);
    
    $tmp_filename='email_preview_'.showDate(time(), 'Y-m-d_H-i-s',1).'_'.rand(10000,99999).'.html';
    $tmp_filepath=GKS_SITE_PATH.'tmp/'.$tmp_filename;
    @file_put_contents($tmp_filepath,$res['body']);
    $tmp_url='admin-get-file.php?fs=tmp&file='.urlencode($tmp_filename);    

    $return = array('success' => true, 'message' => base64_encode('OK'), 'preview_url'=>base64_encode($tmp_url),'subject'=>base64_encode($res['subject']));
    echo json_encode($return); die(); 
    
  } 
  
  
}

$gks_sms_send_insert_id=0;
if ($typeid==3) { //sms
  $template=0;if (isset($_POST['template'])) $template=intval($_POST['template']);
  $message_sms=$message;
  
  $message_sms=str_replace('<br />',"\n",$message_sms);
  $message_sms=str_replace('<br/>',"\n",$message_sms);
  $message_sms=str_replace('<br>',"\n",$message_sms);
  
  $message_sms=str_replace("\n\n","\n",$message_sms);
  $message_sms=str_replace("\n\n","\n",$message_sms);
  $message_sms=str_replace("\n\n","\n",$message_sms);
  $message_sms=str_replace("\n\n","\n",$message_sms);

  $message_order_online=$message_sms;
  
  $sender='';if (isset($_POST['sender_sms'])) $sender=trim_gks(base64_decode($_POST['sender_sms']));
  if ($sender=='') {
    debug_mail(false,'sender is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (1)'));
    echo json_encode($return); die();}
  
  $sender_sms_provider='';if (isset($_POST['sender_sms_provider'])) $sender_sms_provider=trim_gks(base64_decode($_POST['sender_sms_provider']));
  if ($sender_sms_provider=='' or ($sender_sms_provider!='gks_erp_app_mobile' and $sender_sms_provider!='smsapi')) {
    debug_mail(false,'sender_sms_provider is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (2)'));
    echo json_encode($return); die();}
  
  $sender_sms_sender='';if (isset($_POST['sender_sms_sender'])) $sender_sms_sender=trim_gks(base64_decode($_POST['sender_sms_sender']));
  if ($sender_sms_sender=='') {
    debug_mail(false,'sender_sms_sender is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (3)'));
    echo json_encode($return); die();}
  
  if ($sender_sms_provider=='gks_erp_app_mobile') {
    $sql="SELECT id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name
    FROM gks_erp_app_mobile 
    LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
    WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
    and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
    and gks_erp_app_mobile_ping.mydate>='".date('Y-m-d H:i:s', time()- 60*60)."'
    ORDER BY gks_erp_app_mobile.erp_app_mobile_sortorder;";
    //mia ora, to elaxisto einai 15 lepta
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows<=0) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Το συγκεκκριμένο gks ERP App Mobile δεν είναι ενεργό')));
      echo json_encode($return); die();}      
    
    
                
  } else {
    
    
  }

  //echo '<pre>|'.$sender.'|'.$sender_sms_provider.'|'.$sender_sms_sender.'|'; die();
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$sender));
  //echo json_encode($return); die();
    
  $to_sms=0;if (isset($_POST['to_sms'])) $to_sms=trim($_POST['to_sms']);
  if ($to_sms=='') {
    debug_mail(false,'sms user is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποδέκτη')));
    echo json_encode($return); die();}
  
  $send_sms=array();
  $parts=explode(',',$to_sms);
  foreach ($parts as $vmob) {
    $vmob=trim($vmob);
    if ($vmob!='') {
      $send_sms[]=$vmob;
    }
  } 
  //print '<pre>';print_r($send_sms);die();
  
  $list_files_str = trim_gks(base64_decode($_POST['list_files_str']));
  //$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 
  
  $list_files = json_decode($list_files_str, true);
  if ($list_files === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['list_files_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}

  //print '<pre>';print_r($list_files);die();
  $Attachments=array();
  foreach ($list_files as $afile) {
    $afile=trim_gks($afile);
    if ($afile!='') {
      $afile2=GKS_FileServerShare.$afile;
      if (file_exists($afile2)==false) {
        debug_mail(false,'json_decode error',$_POST['list_files_str']);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το αρχείο').': '. mb_basename($afile)));
        echo json_encode($return); die();}
      $Attachments[]=array($afile2,mb_basename($afile));
      
    }
  }
  //print '<pre>';print_r($Attachments);die();
  $myrand=rand(1000,9999).rand(1000,9999);
  $temp_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$myrand.'/';
  if (file_exists($temp_dir) == false) {
    if (@mkdir($temp_dir , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$temp_dir);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '. $temp_dir));
      echo json_encode($return); die();
    }
  }
  
  $url_files=array();
  foreach ($Attachments as $value) {
    if (file_exists($value[0])) {
      $local_path=$temp_dir.$value[1];
      @copy($value[0],$local_path);
      if (file_exists($local_path)) {
        $url_files[]=GKS_SITE_URL.'my/temp/'.$myrand.'/'.$value[1];
      }
    }
  }
  //print '<pre>';print_r($url_files);die();
  
  if (count($url_files)>0) {
    $message_sms.="\n".implode("\n",$url_files);
    $message.='<br>'.implode('<br>',$url_files);
  }
  
  $errors=array();
  foreach ($send_sms as $value) {
    $ret = gks_sms_send($model,$id,$sender_sms_sender,$value,$message_sms,$sender_sms_provider);
    if ($ret==false) $errors[]=gks_lang('Σφάλμα στο').': '.$value;
  }
      
  if (count($errors)>0) {
    debug_mail(false,'sms send error',print_r($errors,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την αποστολή').':<br>'.implode('<br>',$errors)));
    echo json_encode($return); die();}
  
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_POST,true)));echo json_encode($return); die();
  
  
  
}
$sms_id=$gks_sms_send_insert_id;

if ($typeid==4) { //viber
  $template=0;if (isset($_POST['template'])) $template=intval($_POST['template']);
  $message_viber=$message;
  
  $message_viber=str_replace('<br />',"\n",$message_viber);
  $message_viber=str_replace('<br/>',"\n",$message_viber);
  $message_viber=str_replace('<br>',"\n",$message_viber);
  
  $message_viber=str_replace("\n\n","\n",$message_viber);
  $message_viber=str_replace("\n\n","\n",$message_viber);
  $message_viber=str_replace("\n\n","\n",$message_viber);
  $message_viber=str_replace("\n\n","\n",$message_viber);

  $message_order_online=$message_viber;
  
  $to_viber=0;if (isset($_POST['to_viber'])) $to_viber=intval($_POST['to_viber']);
  if ($to_viber<=0) {
    debug_mail(false,'viber user is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποδέκτη')));
    echo json_encode($return); die();}
  
  $sql="SELECT viber_id FROM ".GKS_WP_TABLE_PREFIX."users WHERE viber_id<>'' AND viber_subscribed<>0 and ID=".$to_viber;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
  
  $send_viber=array();
  while ($row = $result->fetch_assoc()) {
    $send_viber[]=$row['viber_id'];
  }
  //print '<pre>';print_r($send_viber);die();
  if (count($send_viber)==0) {
    debug_mail(false,'viber user is not found','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν οι αποδέκτες viber')));
    echo json_encode($return); die();}
    

  
  $list_files_str = trim_gks(base64_decode($_POST['list_files_str']));
  //$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 
  
  $list_files = json_decode($list_files_str, true);
  if ($list_files === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['list_files_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}

  //print '<pre>';print_r($list_files);die();
  $Attachments=array();
  foreach ($list_files as $afile) {
    $afile=trim_gks($afile);
    if ($afile!='') {
      $afile2=GKS_FileServerShare.$afile;
      if (file_exists($afile2)==false) {
        debug_mail(false,'json_decode error',$_POST['list_files_str']);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το αρχείο').': '. mb_basename($afile)));
        echo json_encode($return); die();}
      $Attachments[]=array($afile2,mb_basename($afile));
      
    }
  }
  //print '<pre>';print_r($Attachments);die();
  $myrand=rand(1000,9999).rand(1000,9999);
  $temp_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$myrand.'/';
  if (file_exists($temp_dir) == false) {
    if (@mkdir($temp_dir , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$temp_dir);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '. $temp_dir));
      echo json_encode($return); die();
    }
  }
  
  $url_files=array();
  foreach ($Attachments as $value) {
    if (file_exists($value[0])) {
      $local_path=$temp_dir.$value[1];
      @copy($value[0],$local_path);
      if (file_exists($local_path)) {
        $url_files[]=GKS_SITE_URL.'my/temp/'.$myrand.'/'.$value[1];
      }
    }
  }
  //print '<pre>';print_r($url_files);die();
  
  if (count($url_files)>0) {
    $message_viber.="\n".implode("\n",$url_files);
    $message.='<br>'.implode('<br>',$url_files);
  }
  
  $errors=array();
  foreach ($send_viber as $value) {
    $ret = gks_viber_send($model,$id,$value,$message_viber);
    if (isset($ret['error'])) $errors[]=$ret['error'];
    if (isset($ret['status_message']) and trim_gks(strtolower($ret['status_message']!='ok'))) $errors[]=$ret['status_message'];
    //print '<pre>';print_r($ret);die();
  }
      
  if (count($errors)>0) {
    debug_mail(false,'viber send error',print_r($errors,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την αποστολή').':<br>'.implode('<br>',$errors)));
    echo json_encode($return); die();}
  
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_POST,true)));echo json_encode($return); die();
  
  
  
}


//echo '<pre>';echo $page;die();









$sql="insert into ".$table_name." (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
".$field_pid.",user_id,".$field_text.",email_id,sms_id
) values (
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
".$id.",
".$my_wp_user_id.",
'".$db_link->escape_string($message)."',
0,
".$gks_sms_send_insert_id."
)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$id_rec_message = $db_link->insert_id;


gks_plugins_functions_run('admin_obj_send_message_exec_after_insert',array(
  'id'=>&$id,
  'typeid'=>&$typeid,
  'model'=>&$model,
  'message'=>&$message,
  'table_name' => &$table_name,
  'id_rec_message' => &$id_rec_message,
));



$email_id=0;
if ($typeid==2 and $email_from!='' and $email_to!='') {
  $message_order_online=$message;
  $params=array(
    'model'=>$model,
    'model_id'=>$id,
    'from' => $email_from,
    'to'=>$email_to,
    'subject'=>$subject,
    'template'=>$template,
    'replaces'=>$replaces,
    'Attachments'=>$Attachments,
  );
  
  $gks_mymail_last_email=0;
  $send_email_res = gks_mymail_template($params);
  if ($gks_mymail_last_email>0) {
    $email_id=$gks_mymail_last_email;
    $sql="update ".$table_name." set email_id=".$email_id." where ".$field_id."=".$id_rec_message;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
  }

}

if ($order_online) {

  if ($online_url!='' and $typeid!=2) {
    $message_order_online=str_replace('<a href="'.$online_url.'">'.$online_url.'</a>','',$message_order_online);
    $message_order_online=str_replace('<a href="'.$online_url.'" target="_blank">'.$online_url.'</a>','',$message_order_online);
    $message_order_online=str_replace($online_url,'',$message_order_online);
  }
  if ($typeid!=2) {
    $message_order_online=str_replace("\r\n",'<br>',$message_order_online);
    $message_order_online=str_replace("\n",'<br>',$message_order_online);
  }
  $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio,from_online) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($message_order_online)."',1)";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
}

$html='<tr id="tr_messages_'.$id_rec_message.'">'.
  '<th scope="row" class="mytdcm message_aa">*</th>'.
  '<td class="mytdcml">'.showDate(time(), 'd/m/Y H:i', 1).'</td>'.  
  '<td class="mytdcml">'.wp_get_current_user()->gks_nickname.'</td>'.  
  '<td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.$message.'</div></div></td>'.
  '<td class="mytdcm">'.
  ($email_id==0 ? '' : '<i class="fas fa-envelope gks_email_view" data-id="'.$email_id.'"></i>').
  ($sms_id==0 ? '' :   '<i class="fas fa-sms      gks_sms_view"   data-id="'.$sms_id.  '"></i>').
  '</td>'.
'</tr>';


$user_settings=array();
if ($page=='admin-orders-item.php') {
  $user_settings['gks_orders']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_orders']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_orders']['sms_template']  =$template;$user_settings['gks_orders']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_orders']['viber_template']=$template;
}
if ($page=='admin-acc-inv-item.php') {
  $user_settings['gks_acc_inv']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_acc_inv']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_acc_inv']['sms_template']  =$template;$user_settings['gks_acc_inv']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_acc_inv']['viber_template']=$template;
}
if ($page=='admin-acc-pay-item.php') {
  $user_settings['gks_acc_pay']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_acc_pay']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_acc_pay']['sms_template']  =$template;$user_settings['gks_acc_pay']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_acc_pay']['viber_template']=$template;
}
if ($page=='admin-whi-mov-item.php') {
  $user_settings['gks_whi_mov']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_whi_mov']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_whi_mov']['sms_template']  =$template;$user_settings['gks_whi_mov']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_whi_mov']['viber_template']=$template;
}
if ($page=='admin-hotel-reservation-item.php') {
  $user_settings['gks_hotel_reservation']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_hotel_reservation']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_hotel_reservation']['sms_template']  =$template;$user_settings['gks_hotel_reservation']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_hotel_reservation']['viber_template']=$template;
}
if ($page=='admin-transfer-reservation-item.php') {
  $user_settings['gks_transfer_reservation']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_transfer_reservation']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_transfer_reservation']['sms_template']  =$template;$user_settings['gks_transfer_reservation']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_transfer_reservation']['viber_template']=$template;
}
if ($page=='admin-crm-task-item.php') {
  $user_settings['gks_crm_tasks']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_crm_tasks']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_crm_tasks']['sms_template']  =$template;$user_settings['gks_crm_tasks']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_crm_tasks']['viber_template']=$template;
}
if ($page=='admin-crm-lead-item.php') {
  $user_settings['gks_crm_leads']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_crm_leads']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_crm_leads']['sms_template']  =$template;$user_settings['gks_crm_leads']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_crm_leads']['viber_template']=$template;
}
if ($page=='admin-crm-machine-item.php') {
  $user_settings['gks_crm_machine']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['gks_crm_machine']['email_template']=$template;
  if ($typeid==3) {$user_settings['gks_crm_machine']['sms_template']  =$template;$user_settings['gks_crm_machine']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['gks_crm_machine']['viber_template']=$template;
}
if ($page=='admin-users-item.php') {
  $user_settings['wp_users']['message_type']=$typeid;
  if ($typeid==2)  $user_settings['wp_users']['email_template']=$template;
  if ($typeid==3) {$user_settings['wp_users']['sms_template']  =$template;$user_settings['wp_users']['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings['wp_users']['viber_template']=$template;
}
if ($page=='admin-ct-item.php') {
  $user_settings[$custom_table_name]['message_type']=$typeid;
  if ($typeid==2)  $user_settings[$custom_table_name]['email_template']=$template;
  if ($typeid==3) {$user_settings[$custom_table_name]['sms_template']  =$template;$user_settings[$custom_table_name]['sms_sender']=$sender_sms_provider.':'.$sender_sms_sender;}
  if ($typeid==4)  $user_settings[$custom_table_name]['viber_template']=$template;
  
}

  

//print '<pre>';print_r($user_settings);die();

if (count($user_settings)>0) gks_set_user_settings($my_wp_user_id, $user_settings);


            
$return = array('success' => true, 'message' => base64_encode('OK'),'html' => base64_encode($html),'trid' => $id_rec_message);
echo json_encode($return); die();

function gks_crm_tasks_create_rantevou_ics_file($id,$file_path,$email_to) {
	global $db_link;
	global $GKS_SITE_HUMAN_NAME;
	global $GKS_SITE_EMAIL;
	global $GKS_SITE_NAME;
	global $GKS_CACHE_DB_VER;
	global $gks_cache_version;
	
	$sql=gks_crm_tasks_sql_event("id_crm_task=".$id,'');
	//debug_mail(false,'test sql',$sql);
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
  
  $vcalendar = new Sabre\VObject\Component\VCalendar();
  
  $vcalendar->PRODID='-//gks Software//gks ERP '.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'//EN';
  //$vcalendar->TZID = 'Europe/Athens';
  $vcalendar->METHOD='REQUEST';//REPLY REQUEST
  
  $vevent = $vcalendar->createComponent('VEVENT');
  $uid=trim_gks($row['uid']);
  //echo $uid;die();
  if ($uid == '') $uid=guid_for_calendar_ics();
  $vevent->UID=$uid;
  
  $vtimezone = $vcalendar->add('VTIMEZONE', [
      'TZID'           => 'Europe/Athens',
      'X-LIC-LOCATION' => 'Europe/Athens'
  ]);
  $vevent->TZID='Europe/Athens';

  $dateTime = new \DateTime(showDate(strtotime($row['task_planned_date_from']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTSTART = $dateTime;
  
  
  $dateTime = new \DateTime(showDate(strtotime($row['task_planned_date_to']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTEND =$dateTime;


  $dateTime = new \DateTime(showDate(strtotime($row['mydate_add']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->CREATED = $dateTime;
  
  $dateTime = new \DateTime(showDate(strtotime($row['mydate_edit']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->add('LAST-MODIFIED', $dateTime);    
  

  
  
  $calendar_title = gks_lang('Ραντεβού για').': '.trim_gks($row['subject']);
  if ($calendar_title=='') $calendar_title =gks_lang('Ραντεβού');
  //$calendar_title=trim_gks($calendar_title .' '.$row['first_name'].' '.$row['last_name']);
  
  
  $calendar_message = '';
//  	GKS_SITE_URL.'my/admin-crm-task-item.php?id='.$id."\n".
//  	'Katastasi ergasias: '.$row['task_status_descr']."\n".
//  	'Pelatis: '.trim_gks($row['first_name'].' '.$row['last_name'])."\n".
//  	(isset($row['mobile']) ? 'Kinito: '.trim_gks($row['mobile']) : '')."\n".
//  	(isset($row['phone']) ? 'Mobile: '.trim_gks($row['phone']) : '')."\n".
//  	'Perigrafi: '.trim_gks($row['message']);
  

	$temp=array();
	if (trim_gks($row['odos']) != '') $temp[]= trim_gks($row['odos']);
	if (trim_gks($row['orofos']) != '') $temp[]= trim_gks($row['orofos']);
	if (trim_gks($row['perioxi']) != '') $temp[]= trim_gks($row['perioxi']);
	if (trim_gks($row['poli']) != '') $temp[]= trim_gks($row['poli']);
	if (trim_gks($row['tk']) != '') $temp[]= trim_gks($row['tk']);
	if (trim_gks($row['nomos_descr']) != '') $temp[]= trim_gks($row['nomos_descr']);
	if (trim_gks($row['country_name']) != '') $temp[]= trim_gks($row['country_name']);
	$topothesia=implode(', ',$temp);


	
//	if ($row['map_latitude'] != 0 and $row['map_longitude'] != 0) {
//		//$vevent->GEO = $row['calendar_map_latitude'].';'.$row['calendar_map_longitude'];
//		$vevent->GEO = [$row['map_latitude'], $row['map_longitude']];
//		
//		//$event = $cal->add('VEVENT', ['GEO' => [51.96668, 7.61876],
//		$geo_s='GEO: '.$row['map_latitude'].','.$row['map_longitude'];
//		
//		if (!(strpos($calendar_message, $geo_s) !== false)) {
//		  if ($calendar_message != '') $calendar_message.="\n";
//		  $calendar_message.=$geo_s;
//		}
//	}
  
  
	//$vevent->ORGANIZER='CN="'.$GKS_SITE_HUMAN_NAME.'":mailto:'.$GKS_SITE_EMAIL;

  //$organizer = $vevent->add('ORGANIZER'); //, $GKS_SITE_HUMAN_NAME);
  //$organizer['CN'] = $GKS_SITE_HUMAN_NAME;
  //$organizer['mailto'] = $GKS_SITE_EMAIL;
  $vevent->add('ORGANIZER', 'mailto:'.$GKS_SITE_EMAIL, ['CN' => $GKS_SITE_NAME]); //$GKS_SITE_HUMAN_NAME
//  $vevent->add('ATTENDEE', 'mailto:'.$GKS_SITE_EMAIL, [
//  		'CUTYPE'=>'INDIVIDUAL',
//  		'ROLE'=>'REQ-PARTICIPANT',
//  		'PARTSTAT' => 'ACCEPTED',
//  		'RSVP'=>'TRUE',
//  		'CN' => $GKS_SITE_EMAIL, //$GKS_SITE_HUMAN_NAME,
//  		'X-NUM-GUESTS'=>'0',
//  ]);

//  $PARTSTAT='';
//  if (trim_gks($row_participant['response_type']) == '')          $PARTSTAT='NEEDS-ACTION';
//  else if (trim_gks($row_participant['response_type']) == 'no')   $PARTSTAT='DECLINED';
//  else if (trim_gks($row_participant['response_type']) == 'yes')  $PARTSTAT='ACCEPTED';
//  else if (trim_gks($row_participant['response_type']) == 'isos') $PARTSTAT='TENTATIVE';
  
  
  //$row['user_email_pelatis']
  if (trim_gks($row['gks_nickname_pelatis'])!='') { 
  	$vevent->add('ATTENDEE', 'mailto:'.$email_to, [
  		'CUTYPE'=>'INDIVIDUAL',
  		'ROLE'=>'REQ-PARTICIPANT',
			'PARTSTAT' => 'NEEDS-ACTION',
  		'RSVP'=>'TRUE',
			'CN' => $email_to, //$row['gks_nickname_pelatis'], 
  	  'X-NUM-GUESTS'=>'0',
  	]);
	}
	
//  ORGANIZER;CN=gks.gr;PARTSTAT=ACCEPTED:mailto:info@gks.gr
//  ATTENDEE;CN=Kostas gmail;PARTSTAT=ACCEPTED:mailto:goutoudis@gmail.com


//	ORGANIZER;CN=goutoudis@gmail.com:mailto:goutoudis@gmail.com
//	ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=TRUE
//	 ;CN=goutoudis@gmail.com;X-NUM-GUESTS=0:mailto:goutoudis@gmail.com
//	ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=
//	 TRUE;CN=kostas@gks.gr;X-NUM-GUESTS=0:mailto:kostas@gks.gr



         
  $vevent->DESCRIPTION =$calendar_message;
	
  //$dateTime = new \DateTime(showDate(strtotime($row['mydate_edit']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  //$vevent->add('LAST-MODIFIED', $dateTime);    
	
  $vevent->LOCATION =$topothesia;
  $vevent->SEQUENCE='0';
  $vevent->STATUS='CONFIRMED';
  $vevent->SUMMARY = $calendar_title;
  $vevent->TRANSP ='OPAQUE'; // ($row['calendar_is_exclusive']==1 ? 'OPAQUE' : 'TRANSPARENT');
  
  $valarm = $vcalendar->createComponent('VALARM');
  $valarm->ACTION = 'DISPLAY';
  $valarm->DESCRIPTION =' '.gks_lang('Αυτή είναι μια υπενθύμιση');
  $valarm->TRIGGER = '-P0DT0H60M0S'; //'-PT60M';
  $vevent->add($valarm);
  
  
  $vcalendar->add($vevent);
  
  $vcalendar_str = $vcalendar->serialize();
    	
	if (@file_put_contents($file_path,$vcalendar_str )) return true;
	return false;
}
    		