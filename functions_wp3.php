<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_get_user_communications($id) {
  global $db_link;
  global $gks_voip_params;
  if (isset($gks_voip_params)==false) {
    $gks_voip_params=gks_voip_user_params();
  }
  
  $id=intval($id);
  if ($id<=0) return array();
  
  $ret=array();
  $sql_comm="select * from gks_users_communication where user_id=".$id." and comm_value<>'' order by comm_primary desc, id_user_communication";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
  while ($row_comm = $result_comm->fetch_assoc()) {
    
    $html=$row_comm['comm_value'];
    if ($row_comm['comm_type']=='email') {
      $html='<a href="mailto:'.$row_comm['comm_value'].'">'.$row_comm['comm_value'].'</a>';
    } else if ($row_comm['comm_type']=='phone') {
      $html='<span><a href="tel:'.$row_comm['comm_value'].'"  class="'.$gks_voip_params['class_span'].'">'.$row_comm['comm_value'].'</a>'.$gks_voip_params['html_after_span'].'</span>';
    } else if ($row_comm['comm_type']=='url') {
      $html='<a href="'.$row_comm['comm_value'].'" target="_blank">'.$row_comm['comm_value'].'</a>';
    }
    
    $ret[$row_comm['comm_type']][]=array(
      'id' => $row_comm['id_user_communication'],
      'isp' => $row_comm['comm_primary'],
      'val' => $row_comm['comm_value'],
      'html' => $html,
      'descr' => trim_gks($row_comm['comm_descr']),
    );
    
    
//    if (isset($ret[$row_comm['comm_type']][$row_comm['comm_value']])==false) {
//      $ret[$row_comm['comm_type']][$row_comm['comm_value']]=array('descr' => $row_comm['comm_descr'],'isp' => $row_comm['comm_primary']);
//    } else {
//      $ret[$row_comm['comm_type']][$row_comm['comm_value']]['descr']=$row_comm['comm_descr'];
//      $ret[$row_comm['comm_type']][$row_comm['comm_value']]['isp']=$row_comm['comm_primary'];
//    }
  }
  return $ret;
}


function gks_wp_merge_address_phone_email($id) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $sql="select * from ".GKS_WP_TABLE_PREFIX."users where ID=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  if ($result->num_rows==0) return;
  $row = $result->fetch_assoc();
  $user_email=trim_gks($row['user_email']);
  $user_url=trim_gks($row['user_url']);
  if ($user_email!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='email' and comm_value like '".$db_link->escape_string($user_email)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="update gks_users_communication set comm_primary=0 where user_id=".$id." and comm_type='email'";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'email','".$db_link->escape_string($user_email)."',1
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  if ($user_url!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='url' and comm_value like '".$db_link->escape_string($user_url)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="update gks_users_communication set comm_primary=0 where user_id=".$id." and comm_type='url'";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'url','".$db_link->escape_string($user_url)."',1
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  
  
  $sql="select * from ".GKS_WP_TABLE_PREFIX."usermeta where user_id=".$id." and (meta_key like 'billing_%' or meta_key like 'shipping_%') order by meta_key";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
        
  $data=[];
  while ($row = $result->fetch_assoc()) {
    $data[$row['meta_key']]=trim_gks($row['meta_value']);
  }
  
  $sql="select * from gks_users where user_id=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_users (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id."
    )";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_title='';
    $exist_ma_odos='';
    $exist_ma_orofos='';
    $exist_ma_perioxi='';
    $exist_ma_poli='';
    $exist_ma_tk='';
    $exist_ma_country_id=0;
    $exist_ma_nomos_id=0;
  } else {
    $row = $result->fetch_assoc();
    $exist_title=trim_gks($row['title']);
    $exist_ma_odos=trim_gks($row['ma_odos']);
    $exist_ma_orofos=trim_gks($row['ma_orofos']);
    $exist_ma_perioxi=trim_gks($row['ma_perioxi']);
    $exist_ma_poli=trim_gks($row['ma_poli']);
    $exist_ma_tk=trim_gks($row['ma_tk']);
    $exist_ma_country_id=intval($row['ma_country_id']);
    $exist_ma_nomos_id=intval($row['ma_nomos_id']);
  }
  
  $sqlUpdate='';

  
  if (isset($data['billing_company']) and $data['billing_company']!='' and $exist_title=='') {
    $sqlUpdate.="title='".$db_link->escape_string($data['billing_company'])."',";  
  }
  if (((isset($data['billing_address_1']) and trim_gks($data['billing_address_1'])!='') or (isset($data['billing_address_2']) and trim_gks($data['billing_address_2'])!='')) and $exist_ma_odos=='') {
    $ma_odos1=(isset($data['billing_address_1']) ? trim_gks($data['billing_address_1']) : '');
    $ma_odos2=(isset($data['billing_address_2']) ? trim_gks($data['billing_address_2']) : '');
    $ma_odos='';
    if ($ma_odos1!='' and $ma_odos2!='') $ma_odos=$ma_odos1.', '.$ma_odos2;
    else if ($ma_odos1!='' and $ma_odos2=='') $ma_odos=$ma_odos1;
    else if ($ma_odos1=='' and $ma_odos2!='') $ma_odos=$ma_odos2;
    $sqlUpdate.="ma_odos='".$db_link->escape_string($ma_odos)."',";  
  }
  if (isset($data['billing_city']) and $data['billing_city']!='' and $exist_ma_poli=='') {
    $sqlUpdate.="ma_poli='".$db_link->escape_string($data['billing_city'])."',";  
  }
  if (isset($data['billing_postcode']) and $data['billing_postcode']!='' and $exist_ma_tk=='') {
    $sqlUpdate.="ma_tk='".$db_link->escape_string($data['billing_postcode'])."',";  
  }
  if (isset($data['billing_country']) and $data['billing_country']!='' and ($exist_ma_country_id==0 or $exist_ma_country_id==91)) {
    $sql="SELECT id_country FROM gks_country WHERE country_initials='".$db_link->escape_string($data['billing_country'])."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $ma_country_id=intval($row['id_country']);
      if ($exist_ma_country_id!=$ma_country_id) {
        $sqlUpdate.="ma_country_id=".$ma_country_id.",";  
      }
    }
  }
  
  
  //echo '<pre>';
  if ($sqlUpdate!='') {
    $sqlUpdate.="mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'"; 
    $sql="update gks_users set ". $sqlUpdate." where user_id=".$id." limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    //echo $sqlUpdate."\n";
  }
  
  $billing_email=''; if (isset($data['billing_email'])) $billing_email=trim_gks($data['billing_email']);
  if ($billing_email!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='email' and comm_value like '".$db_link->escape_string($billing_email)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary,comm_descr
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'email','".$db_link->escape_string($billing_email)."',0,
      '".$db_link->escape_string(gks_lang('Τιμολόγησης'))."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  $billing_phone=''; if (isset($data['billing_phone'])) $billing_phone=trim_gks($data['billing_phone']);
  if ($billing_phone!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='phone' and comm_value like '".$db_link->escape_string($billing_phone)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary,comm_descr
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'phone','".$db_link->escape_string($billing_phone)."',0,
      '".$db_link->escape_string(gks_lang('Τιμολόγησης'))."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  $billing_phone_2=''; if (isset($data['billing_phone_2'])) $billing_phone_2=trim_gks($data['billing_phone_2']);
  if ($billing_phone_2!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='phone' and comm_value like '".$db_link->escape_string($billing_phone_2)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary,comm_descr
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'phone','".$db_link->escape_string($billing_phone_2)."',0,
      '".$db_link->escape_string(gks_lang('Τιμολόγησης'))."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  
  

  
  $shipping_phone=''; if (isset($data['shipping_phone'])) $shipping_phone=trim_gks($data['shipping_phone']);
  if ($shipping_phone!='') {
    $sql="select * from gks_users_communication where user_id=".$id." and comm_type='phone' and comm_value like '".$db_link->escape_string($shipping_phone)."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,comm_type,comm_value,comm_primary,comm_descr
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",'phone','".$db_link->escape_string($shipping_phone)."',0,
      '".$db_link->escape_string(gks_lang('Αποστολής'))."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  
  
  
  
  //print_r($data);
  
  
  $sql="select * from gks_users_extra_address where user_id=".$id." and is_woo_delivery=1";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $id_users_extra_address=0;
    $exist_ea_name='';
    $exist_ea_phone='';
    $exist_ea_odos='';
    $exist_ea_orofos='';
    $exist_ea_perioxi='';
    $exist_ea_poli='';
    $exist_ea_tk='';
    $exist_ea_country_id=0;
    $exist_ea_nomos_id=0;
  } else {
    $row = $result->fetch_assoc();
    $id_users_extra_address=$row['id_users_extra_address'];
    $exist_ea_name=trim_gks($row['ea_name']);
    $exist_ea_phone=trim_gks($row['ea_phone']);
    $exist_ea_odos=trim_gks($row['ea_odos']);
    $exist_ea_orofos=trim_gks($row['ea_orofos']);
    $exist_ea_perioxi=trim_gks($row['ea_perioxi']);
    $exist_ea_poli=trim_gks($row['ea_poli']);
    $exist_ea_tk=trim_gks($row['ea_tk']);
    $exist_ea_country_id=intval($row['ea_country_id']);
    $exist_ea_nomos_id=intval($row['ea_nomos_id']);
  }  
  
  $sqlUpdate='';

  if (isset($data['shipping_phone']) and $data['shipping_postcode']!='' and $exist_ea_phone=='') {
    $sqlUpdate.="ea_phone='".$db_link->escape_string($data['shipping_phone'])."',";  
  }


  $shipping_first_name='';if (isset($data['shipping_first_name'])) $shipping_first_name=trim_gks($data['shipping_first_name']);
  $shipping_last_name=''; if (isset($data['shipping_last_name']))  $shipping_last_name=trim_gks($data['shipping_last_name']);
  $ea_name='';
  if ($shipping_first_name!='' and $shipping_last_name!='') $ea_name=$shipping_first_name.', '.$shipping_last_name;
  else if ($shipping_first_name!='' and $shipping_last_name=='') $ea_name=$shipping_first_name;
  else if ($shipping_first_name=='' and $shipping_last_name!='') $ea_name=$shipping_last_name;
  if ($ea_name!='' and $exist_ea_name=='') {
    $sqlUpdate.="ea_name='".$db_link->escape_string($ea_name)."',";  
  }
    
  if (((isset($data['shipping_address_1']) and trim_gks($data['shipping_address_1'])!='') or (isset($data['shipping_address_2']) and trim_gks($data['shipping_address_2'])!='')) and $exist_ea_odos=='') {
    $ea_odos1=(isset($data['shipping_address_1']) ? trim_gks($data['shipping_address_1']) : '');
    $ea_odos2=(isset($data['shipping_address_2']) ? trim_gks($data['shipping_address_2']) : '');
    $ea_odos='';
    if ($ea_odos1!='' and $ea_odos2!='') $ea_odos=$ea_odos1.', '.$ea_odos2;
    else if ($ea_odos1!='' and $ea_odos2=='') $ea_odos=$ea_odos1;
    else if ($ea_odos1=='' and $ea_odos2!='') $ea_odos=$ea_odos2;
    $sqlUpdate.="ea_odos='".$db_link->escape_string($ea_odos)."',";  
  }
  if (isset($data['shipping_city']) and $data['shipping_city']!='' and $exist_ea_poli=='') {
    $sqlUpdate.="ea_poli='".$db_link->escape_string($data['shipping_city'])."',";  
  }
  if (isset($data['shipping_postcode']) and $data['shipping_postcode']!='' and $exist_ea_tk=='') {
    $sqlUpdate.="ea_tk='".$db_link->escape_string($data['shipping_postcode'])."',";  
  }
  if (isset($data['shipping_country']) and $data['shipping_country']!='' and ($exist_ea_country_id==0 or $exist_ea_country_id==91)) {
    $sql="SELECT id_country FROM gks_country WHERE country_initials='".$db_link->escape_string($data['shipping_country'])."'";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $ea_country_id=intval($row['id_country']);
      if ($exist_ea_country_id!=$ea_country_id) {
        $sqlUpdate.="ea_country_id=".$ea_country_id.",";  
      }
    }
  }
  
  if ($sqlUpdate!='') {
    if ($id_users_extra_address==0) {
      $sql="insert into gks_users_extra_address (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,is_woo_delivery
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id.",1
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $id_users_extra_address=$db_link->insert_id;  
    }
    $sqlUpdate.="mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'"; 
    $sql="update gks_users_extra_address set ". $sqlUpdate." where id_users_extra_address=".$id_users_extra_address." limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    //echo $sqlUpdate."\n";
  }  
  
  
  
  
  //echo $sqlUpdate."\n";die();
  
}
/*

(GKS_ERP_START_VARDIA==0 ? 6 : 5)
'has_custom_default' => 5
'has_custom_default' => 6
'today'
  'vals' => gks_filter_date_vals(['field'=>'gks_acc_inv.inv_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

  'vals' => gks_filter_date_vals(['field'=>'gks_orders.ddate','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia,'extra10'=>array(
  
  )]),

*/


function gks_filter_date_vals($params) {
  $field='';       if (isset($params['field'])) $field=$params['field'];
  $future=false;   if (isset($params['future'])) $future=$params['future'];
  $today='';       if (isset($params['today'])) $today=$params['today'];
  $today_vardia='';if (isset($params['today_vardia'])) $today_vardia=$params['today_vardia'];
  $local_time=false;if (isset($params['local_time'])) $local_time=$params['local_time'];
  
  $wd=$today;
  if (GKS_ERP_START_VARDIA!=0 and $today_vardia!='') $wd=$today_vardia;
  $set_vardia=GKS_ERP_START_VARDIA!=0;
  if (isset($params['set_vardia'])) {
    $set_vardia=$params['set_vardia'];
    if ($set_vardia==false) $wd=$today;
  }
  
  $vals = array();
  
  $vals[]=array('value' => 1, 'text' => gks_lang('Όλα'),     'sql' => "1=1");
  if (isset($params['extra10'])) {
    foreach ($params['extra10'] as $value) $vals[]=$value;
  }
  
  if ($future) {
    $vals[]=array('value' => 2,
          				'text' => gks_lang('Μέλλον'),
          				'sql' => $field.">= '{$wd}'");
    $vals[]=array('value' => 37,
          				'text' => gks_lang('Επόμενοι 3 μήνες'),
          				'sql' => $field.">= '{$wd}' and ".$field."<= DATE_ADD('{$wd}', INTERVAL 3 MONTH)");  		
    $vals[]=array('value' => 38,
          				'text' => gks_lang('Επόμενος μήνας'),
          				'sql' => $field.">= '{$wd}' and ".$field."<= DATE_ADD('{$wd}', INTERVAL 1 MONTH)");
    $vals[]=array('value' => 39,
          				'text' => gks_lang('Επόμενη εβδομάδα'),
          				'sql' => $field.">= '{$wd}' and ".$field."<= DATE_ADD('{$wd}', INTERVAL 1 WEEK)");
    $vals[]=array('value' => 40,
          				'text' => vardia_name($wd, 8),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 8 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 9 DAY)");
    $vals[]=array('value' => 41,
          				'text' => vardia_name($wd, 7),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 7 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 8 DAY)");
    $vals[]=array('value' => 42,
          				'text' => vardia_name($wd, 6),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 6 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 7 DAY)");
    $vals[]=array('value' => 43,
          				'text' => vardia_name($wd, 5),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 5 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 6 DAY)");
    $vals[]=array('value' => 44,
          				'text' => vardia_name($wd, 4),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 4 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 5 DAY)");
    $vals[]=array('value' => 45,
          				'text' => vardia_name($wd, 3),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 3 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 4 DAY)");
    $vals[]=array('value' => 46,
          				'text' => vardia_name($wd, 2),
          				'sql' => $field.">= DATE_ADD('{$wd}', INTERVAL 2 DAY) AND ".$field."< DATE_ADD('{$wd}', INTERVAL 3 DAY)");
  	if ($set_vardia) 
    $vals[]=array('value' => 3,
    								'text' => gks_lang('Αυριανή Βάρδια'),
    								'sql' => $field.">= DATE_ADD('{$today_vardia}', INTERVAL 1 DAY) AND ".$field."< DATE_ADD('{$today_vardia}', INTERVAL 2 DAY)");
    $vals[]=array('value' => 4,
          				'text' => gks_lang('Αύριο'),
          				'sql' => $field.">= DATE_ADD('{$today}', INTERVAL 1 DAY) AND ".$field."< DATE_ADD('{$today}', INTERVAL 2 DAY)");
  }
	if ($set_vardia) {
  	$vals[]=array('value' => 5,
  								'text' => gks_lang('Σημερινή Βάρδια'),
  								'sql' => $field.">= '{$today_vardia}' and ".$field." < DATE_ADD('{$today_vardia}', INTERVAL 1 DAY)");
	}
  $vals[]=array('value' => 6,
        				'text' => gks_lang('Σήμερα'),
        				'sql' => $field.">= '{$today}' and ".$field."< DATE_ADD('{$today}', INTERVAL 1 DAY)");
	if ($set_vardia) {
  	$vals[]=array('value' => 7,
  								'text' => gks_lang('Χθεσινή Βάρδια'),
  								'sql' => $field.">= DATE_SUB('{$today_vardia}', INTERVAL 1 DAY) AND ".$field."< '{$today_vardia}'");
  }
  $vals[]=array('value' => 8,
        				'text' => gks_lang('Χθες'),
        				'sql' => $field.">= DATE_SUB('{$today}', INTERVAL 1 DAY) AND ".$field."< '{$today}'");
  $vals[]=array('value' => 9,
        				'text' => vardia_name($wd, -2),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 2 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 1 DAY)");
  $vals[]=array('value' => 10,
        				'text' => vardia_name($wd, -3),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 3 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 2 DAY)");
  $vals[]=array('value' => 11,
        				'text' => vardia_name($wd, -4),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 4 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 3 DAY)");
  $vals[]=array('value' => 12,
        				'text' => vardia_name($wd, -5),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 5 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 4 DAY)");
  $vals[]=array('value' => 13,
        				'text' => vardia_name($wd, -6),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 6 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 5 DAY)");
  $vals[]=array('value' => 14,
        				'text' => vardia_name($wd, -7),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 7 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 6 DAY)");
  $vals[]=array('value' => 15,
        				'text' => vardia_name($wd, -8),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 8 DAY) AND ".$field."< DATE_SUB('{$wd}', INTERVAL 7 DAY)");
  $vals[]=array('value' => 16,
        				'text' => gks_lang('Προηγούμενη εβδομάδα'),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 1 WEEK) and ".$field."< DATE_ADD('{$wd}', INTERVAL 1 DAY)");
  $vals[]=array('value' => 17,
        				'text' => gks_lang('Προηγούμενος μήνας'),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 1 MONTH) and ".$field."< DATE_ADD('{$wd}', INTERVAL 1 DAY)");
  $vals[]=array('value' => 18,
        				'text' => gks_lang('Προηγούμενοι 3 μήνες'),
        				'sql' => $field.">= DATE_SUB('{$wd}', INTERVAL 3 MONTH) and ".$field."< DATE_ADD('{$wd}', INTERVAL 1 DAY)");  		
  $vals[]=array('value' => 19,
        				'text' => gks_lang('Παρελθόν'),
        				'sql' => $field."< '" .date('Y-m-d H:i:s',strtotime($wd) + 24*60*60). "'");
  $vals[]=array('value' => -2,
        				'text' => gks_lang('Επιλογή ημερών').($set_vardia ? ' '.gks_lang('(με βάρδια)') : ''),
        				'sql' => '',
        				'is_custom_date' => true,
        				'vardiacustomdate' => ($set_vardia ? true : false),
        				'local_time' => $local_time,
        				);

		

  return $vals;  
}


function gks_voip_remote_localdb_update($user_id) {
  $user_id=intval($user_id);
  $url='cron_voip.php?user_id='.$user_id;
  gks_erp_cron_curl_fnc($url);
}

