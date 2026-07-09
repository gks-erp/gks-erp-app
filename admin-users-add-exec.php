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



$my_page_title=gks_lang('Αποθήκευση νέας επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id,'wp_users','add',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


  

$dr_user_first_name=''; if (isset($_POST['dr_user_first_name'])) $dr_user_first_name=trim_gks(base64_decode($_POST['dr_user_first_name']));
$dr_user_last_name=''; if (isset($_POST['dr_user_last_name'])) $dr_user_last_name=trim_gks(base64_decode($_POST['dr_user_last_name']));
$dr_user_email=''; if (isset($_POST['dr_user_email'])) $dr_user_email=trim_gks(base64_decode($_POST['dr_user_email']));
$dr_user_mobile=''; if (isset($_POST['dr_user_mobile'])) $dr_user_mobile=trim_gks(base64_decode($_POST['dr_user_mobile']));
$dr_user_mobile=str_replace(' ', '', $dr_user_mobile);

$dr_user_phone=''; if (isset($_POST['dr_user_phone'])) $dr_user_phone=trim_gks(base64_decode($_POST['dr_user_phone']));
$dr_user_phone=str_replace(' ', '', $dr_user_phone);

if ($dr_user_phone=='' and $dr_user_mobile!='' and substr($dr_user_mobile,0,1)!='6') {
  $dr_user_phone=$dr_user_mobile;
  $dr_user_mobile='';
}

$genisi_date='';
if (isset($_POST['genisi_date'])) {
  if ($_POST['genisi_date'] == '__/__/____') $_POST['genisi_date']='';
  $genisi_date=trim_gks(stripslashes(urldecode($_POST['genisi_date'])));
  if ($genisi_date!='') {
    $genisi_date = mystrtodb_s($genisi_date.' 00:00:00');
  }
}
$user_url=''; if (isset($_POST['user_url'])) $user_url=trim_gks(base64_decode($_POST['user_url']));
$ma_latitude=0; if (isset($_POST['ma_latitude'])) $ma_latitude=floatval(str_replace(',','.', $_POST['ma_latitude']));
$ma_longitude=0; if (isset($_POST['ma_longitude'])) $ma_longitude=floatval(str_replace(',','.', $_POST['ma_longitude']));

$dr_user_lang=''; if (isset($_POST['dr_user_lang'])) $dr_user_lang=trim_gks(base64_decode($_POST['dr_user_lang']));
$dr_user_ma_odos=''; if (isset($_POST['dr_user_ma_odos'])) $dr_user_ma_odos=trim_gks(base64_decode($_POST['dr_user_ma_odos']));
$dr_user_ma_arithmos=''; if (isset($_POST['dr_user_ma_arithmos'])) $dr_user_ma_arithmos=trim_gks(base64_decode($_POST['dr_user_ma_arithmos']));
$dr_user_ma_orofos=''; if (isset($_POST['dr_user_ma_orofos'])) $dr_user_ma_orofos=trim_gks(base64_decode($_POST['dr_user_ma_orofos']));
$dr_user_ma_perioxi=''; if (isset($_POST['dr_user_ma_perioxi'])) $dr_user_ma_perioxi=trim_gks(base64_decode($_POST['dr_user_ma_perioxi']));
$dr_user_ma_poli=''; if (isset($_POST['dr_user_ma_poli'])) $dr_user_ma_poli=trim_gks(base64_decode($_POST['dr_user_ma_poli']));
$dr_user_ma_tk=''; if (isset($_POST['dr_user_ma_tk'])) $dr_user_ma_tk=trim_gks(base64_decode($_POST['dr_user_ma_tk']));
$dr_user_ma_country_id=0; if (isset($_POST['dr_user_ma_country_id'])) $dr_user_ma_country_id=intval($_POST['dr_user_ma_country_id']);
$dr_user_ma_nomos_id=0; if (isset($_POST['dr_user_ma_nomos_id'])) $dr_user_ma_nomos_id=intval($_POST['dr_user_ma_nomos_id']);
$form_parastatiko=0; if (isset($_POST['form_parastatiko'])) $form_parastatiko=intval($_POST['form_parastatiko']);

//if ($form_parastatiko == 0) {
//  $dr_user_eponimia=''; 
//  $dr_user_title=''; 
//  $dr_user_afm=''; 
//  $dr_user_doy=''; 
//  $dr_user_epaggelma='';
//} else {
  $dr_user_eponimia=''; if (isset($_POST['dr_user_eponimia'])) $dr_user_eponimia=trim_gks(base64_decode($_POST['dr_user_eponimia']));
  $dr_user_title=''; if (isset($_POST['dr_user_title'])) $dr_user_title=trim_gks(base64_decode($_POST['dr_user_title']));
  $dr_user_afm=''; if (isset($_POST['dr_user_afm'])) $dr_user_afm=trim_gks(base64_decode($_POST['dr_user_afm']));
  $dr_user_doy=''; if (isset($_POST['dr_user_doy'])) $dr_user_doy=trim_gks(base64_decode($_POST['dr_user_doy']));
  $dr_user_epaggelma=''; if (isset($_POST['dr_user_epaggelma'])) $dr_user_epaggelma=trim_gks(base64_decode($_POST['dr_user_epaggelma']));
//}

//$form_select_apostoli=-1; if (isset($_POST['form_select_apostoli'])) $form_select_apostoli=intval($_POST['form_select_apostoli']);
//$form_ea_name=''; if (isset($_POST['form_ea_name'])) $form_ea_name=trim_gks(base64_decode($_POST['form_ea_name']));
//$form_ea_phone=''; if (isset($_POST['form_ea_phone'])) $form_ea_phone=trim_gks(base64_decode($_POST['form_ea_phone']));
//$form_ea_odos=''; if (isset($_POST['form_ea_odos'])) $form_ea_odos=trim_gks(base64_decode($_POST['form_ea_odos']));
//$form_ea_perioxi=''; if (isset($_POST['form_ea_perioxi'])) $form_ea_perioxi=trim_gks(base64_decode($_POST['form_ea_perioxi']));
//$form_ea_poli=''; if (isset($_POST['form_ea_poli'])) $form_ea_poli=trim_gks(base64_decode($_POST['form_ea_poli']));
//$form_ea_tk=''; if (isset($_POST['form_ea_tk'])) $form_ea_tk=trim_gks(base64_decode($_POST['form_ea_tk']));
//$form_ea_country_id=0; if (isset($_POST['form_ea_country_id'])) $form_ea_country_id=intval($_POST['form_ea_country_id']);
//$form_ea_nomos_id=0; if (isset($_POST['form_ea_nomos_id'])) $form_ea_nomos_id=intval($_POST['form_ea_nomos_id']);


$fiscal_position_id=0; if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);  
$pricelist_id=0; if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);  
$def_ekptosi=0;  if (isset($_POST['def_ekptosi']))  $def_ekptosi=floatval($_POST['def_ekptosi']);

$force=0;if (isset($_POST['force'])) $force=intval($_POST['force']);  
$select_user_id=0;if (isset($_POST['select_user_id'])) $select_user_id=intval($_POST['select_user_id']);  

if ($dr_user_email == '' and $dr_user_first_name == '' and $dr_user_last_name == '' and $dr_user_eponimia == '' and $dr_user_title == '') {
  debug_mail(false,'record not found',                           gks_lang('Εισάγετε τουλάχιστον το Όνομα ή Επίθετο ή email ή Επωνυμία εταιρείας ή Τίτλο εταιρείας'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε τουλάχιστον το Όνομα ή Επίθετο ή email ή Επωνυμία εταιρείας ή Τίτλο εταιρείας')));
  echo json_encode($return); die();  }  
  


$add_user=false;

$exist_rows=array();
if ($force==0) {
 if ($dr_user_email!='') {
    $sql="select ID, gks_nickname,user_email from ".GKS_WP_TABLE_PREFIX."users where user_email like '".$db_link->escape_string($dr_user_email)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      while ($row = $result->fetch_assoc()) {
        if (isset($exist_rows[$row['ID']]) == false) $exist_rows[$row['ID']]=array('ID' =>$row['ID'], 'gks_nickname'=>$row['gks_nickname'], 'descrs' => array());
        $exist_rows[$row['ID']]['descrs'][] = gks_lang('Με βάση το email').' <b>'.$row['user_email'].'</b>';
      }
    }
  }
  
  if ($dr_user_mobile!='' and strlen($dr_user_mobile)>=8) {
    $sql="select ID, gks_nickname, gks_mobile from ".GKS_WP_TABLE_PREFIX."users where gks_mobile like '%".$db_link->escape_string($dr_user_mobile)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      while ($row = $result->fetch_assoc()) {
        if (isset($exist_rows[$row['ID']]) == false) $exist_rows[$row['ID']]=array('ID' =>$row['ID'], 'gks_nickname'=>$row['gks_nickname'], 'descrs' => array());
        $exist_rows[$row['ID']]['descrs'][] = gks_lang('Με βάση το κινητό').' <b>'.$row['gks_mobile'].'</b>';
      }
    }
  }
  
  $found_one_afm=false;
  if ($dr_user_afm!='' and $dr_user_ma_country_id>0) {
    $sql="SELECT gks_users.afm, ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_country.country_name
    FROM (gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null and gks_users.afm like '".$db_link->escape_string($dr_user_afm)."' and gks_users.ma_country_id=".$dr_user_ma_country_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      while ($row = $result->fetch_assoc()) {
        if (isset($exist_rows[$row['ID']]) == false) $exist_rows[$row['ID']]=array('ID' =>$row['ID'], 'gks_nickname'=>$row['gks_nickname'], 'descrs' => array());
        $tttt=gks_lang('Με βάση την χώρα <b>[1]</b> και το ΑΦΜ  <b>[2]</b>');
        $tttt=str_replace('[1]',$row['country_name'],$tttt);
        $tttt=str_replace('[2]',$row['afm'],$tttt);
        $exist_rows[$row['ID']]['descrs'][] = $tttt;
        $found_one_afm=true;
      }
    }
  }
  if ($dr_user_afm!='' and ($dr_user_ma_country_id == 0 or $found_one_afm==false)) {
    $sql="SELECT gks_users.afm, ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null and gks_users.afm like '".$db_link->escape_string($dr_user_afm)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      while ($row = $result->fetch_assoc()) {
        if (isset($exist_rows[$row['ID']]) == false) $exist_rows[$row['ID']]=array('ID' =>$row['ID'], 'gks_nickname'=>$row['gks_nickname'], 'descrs' => array());
        $exist_rows[$row['ID']]['descrs'][] = gks_lang('Με βάση το ΑΦΜ').' <b>'.$row['afm'].'</b>';
      }
    }  
  }
  
  $exist_rows_out=array();
  foreach ($exist_rows as $value) {
     $exist_rows_out[]=$value;
  } 
  if (count($exist_rows) > 0) {
    
    $return = array('success' => true,'ask_user'=>true, 'message' => base64_encode('OK'), 'exist_rows' => $exist_rows_out, 'balance_user_before' => 0);
    echo json_encode($return); die();
  }
  $add_user=true;
} else {

  //force =1
  if ($select_user_id >0) {
    $sql="select ID, gks_nickname, gks_balance from ".GKS_WP_TABLE_PREFIX."users where ID =".$select_user_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows==0) {  
      debug_mail(false,'record not found',                           gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();  }
    
    $row = $result->fetch_assoc();
    $balance_user_before=floatval($row['gks_balance']);
    
    if (isset($_POST['order_id']) and intval($_POST['order_id'])>0) {
      $balance_user_before=gks_balance_calc(['id' => $select_user_id, 'except_id_order' => intval($_POST['order_id'])]);
    } else if (isset($_POST['acc_inv_id']) and intval($_POST['acc_inv_id'])>0) {
      $balance_user_before=gks_balance_calc(['id' => $select_user_id, 'except_id_acc_inv' => intval($_POST['acc_inv_id'])]);
    } else {
      $balance_user_before=floatval($row['gks_balance']);
    }
    
    $return = array('success' => true,'ask_user'=>false, 'message' => base64_encode('OK'), 'exist_rows' => array(), 'user_id' => intval($row['ID']),'gks_nickname' => trim_gks($row['gks_nickname']), 'balance_user_before' => $balance_user_before);
    echo json_encode($return); die();    
    
  } else {
    $add_user=true;
  }
}

if ($add_user) {
  
  $sql="select max(id) as cc from ".GKS_WP_TABLE_PREFIX."users";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
   
  $row = $result->fetch_assoc();
  $maxid=$row['cc'] + 1;
  
  $sql="insert into ".GKS_WP_TABLE_PREFIX."users (
  mydate_add,user_id_add,myip,
  mydate_edit,user_id_edit,
  user_login,display_name,user_nicename,gks_nickname,user_registered,update_from_gks,user_url
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  now(),".$my_wp_user_id.",
  'user".$maxid."','user".$maxid."','user".$maxid."','user".$maxid."',NOW(),1,'".$db_link->escape_string($user_url)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $user_id = $db_link->insert_id;
  
  $sql="insert into ".GKS_WP_TABLE_PREFIX."usermeta (user_id,meta_key,meta_value) values (".$user_id.",'nickname','user".$maxid."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'admin-users-item.php error sql',$sql);
    die('sql error');
  }  
  
  $user_object = new WP_User($user_id);
  $user_object->add_role('customer');
  
  
  $gks_nickname="user".$maxid;
  if ($dr_user_first_name!='' and $dr_user_last_name!='') $gks_nickname=$dr_user_first_name.' '.$dr_user_last_name;
  else if ($dr_user_first_name!='') $gks_nickname=$dr_user_first_name;
  else if ($dr_user_last_name!='') $gks_nickname=$dr_user_last_name;
  else if ($dr_user_title!='') $gks_nickname=$dr_user_title;
  else if ($dr_user_eponimia!='') $gks_nickname=$dr_user_eponimia;
  else if ($dr_user_email!='') $gks_nickname=$dr_user_email;
  
  $user_login="user".$maxid;
  $user_pass_pure=rand(10000,99999);
  $display_name=$gks_nickname;
  
  $sql="update ".GKS_WP_TABLE_PREFIX."users set 
  user_pass='".$db_link->escape_string(wp_hash_password($user_pass_pure))."',
  user_pass_pure='".$db_link->escape_string($user_pass_pure)."',

  user_login='".$db_link->escape_string($user_login)."',
  user_nicename='".$db_link->escape_string($user_login)."',
  user_email='".$db_link->escape_string($dr_user_email)."',
  display_name='".$db_link->escape_string($display_name)."',
  fiscal_position_id=".$fiscal_position_id.",
  pricelist_id=".$pricelist_id.",
  generic_ekprosi=".number_format($def_ekptosi,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
  user_activation_key='',
  user_status=0,
  
  gks_lang='".$db_link->escape_string($dr_user_lang)."',
  
  update_from_gks=1
  where id=".$user_id." limit 1";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  update_user_meta( $user_id, 'nickname', $gks_nickname);
  update_user_meta( $user_id, 'first_name', $dr_user_first_name);
  update_user_meta( $user_id, 'last_name', $dr_user_last_name);
  update_user_meta( $user_id, 'display_name', $display_name);
  update_user_meta( $user_id, 'mobile', $dr_user_mobile);
  
  if ($dr_user_email!='') {
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",'email','".$db_link->escape_string($dr_user_email)."','".$db_link->escape_string(gks_lang('Εργασίας'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'error sql',$sql_comm);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  if ($dr_user_mobile!='') {
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",'phone','".$db_link->escape_string($dr_user_mobile)."','".$db_link->escape_string(gks_lang('Κινητό'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'error sql',$sql_comm);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  if ($dr_user_phone!='') {
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",'phone','".$db_link->escape_string($dr_user_phone)."','".$db_link->escape_string(gks_lang('Εργασίας'))."',".($dr_user_mobile=='' ? '1' : '0')."
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'error sql',$sql_comm);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  if ($user_url!='') {
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",'url','".$db_link->escape_string($user_url)."','".$db_link->escape_string(gks_lang('Εργασίας'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'error sql',$sql_comm);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
      
  $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$user_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
  $result_gks_users = $db_link->query($sql);   
    
  
  $sql="update gks_users set  
  genisi_date=".($genisi_date == '' ? 'null' : "'".$db_link->escape_string($genisi_date)."'") .", 
  phone_home = '".$db_link->escape_string($dr_user_phone)."',
  ma_latitude=".number_format($ma_latitude,16,'.','').",
  ma_longitude=".number_format($ma_longitude,16,'.','').",

  eponimia = '".$db_link->escape_string($dr_user_eponimia)."',
  title = '".$db_link->escape_string($dr_user_title)."',
  afm = '".$db_link->escape_string($dr_user_afm)."',
  doy = '".$db_link->escape_string($dr_user_doy)."',
  epaggelma = '".$db_link->escape_string($dr_user_epaggelma)."',
  ma_odos = '".$db_link->escape_string($dr_user_ma_odos)."',
  ma_arithmos = '".$db_link->escape_string($dr_user_ma_arithmos)."',
  ma_orofos = '".$db_link->escape_string($dr_user_ma_orofos)."',
  ma_perioxi = '".$db_link->escape_string($dr_user_ma_perioxi)."',
  ma_poli = '".$db_link->escape_string($dr_user_ma_poli)."',
  ma_tk = '".$db_link->escape_string($dr_user_ma_tk)."',
  ma_country_id = ".$dr_user_ma_country_id.",
  ma_nomos_id = ".$dr_user_ma_nomos_id.",
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  
  where user_id=".$user_id." limit 1";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  

  calc_profilepososto($user_id,true);
  
  
  gks_plugins_functions_run('admin_users_add_exec_after',array(
    'user_id'=>&$user_id,
    'acc_inv_id'=>(isset($_POST['acc_inv_id']) ? $_POST['acc_inv_id'] : 0),
    'whi_mov_id'=>(isset($_POST['whi_mov_id']) ? $_POST['whi_mov_id'] : 0),
    'hotel_reservation_id'=>(isset($_POST['hotel_reservation_id']) ? $_POST['hotel_reservation_id'] : 0),
    'transfer_reservation_id'=>(isset($_POST['transfer_reservation_id']) ? $_POST['transfer_reservation_id'] : 0),
    'order_id'=>(isset($_POST['order_id']) ? $_POST['order_id'] : 0),
    'journal_id'=>(isset($_POST['journal_id']) ? $_POST['journal_id'] : 0),
    'seira_id'=>(isset($_POST['seira_id']) ? $_POST['seira_id'] : 0),
    'crm_lead_id'=>(isset($_POST['crm_lead_id']) ? $_POST['crm_lead_id'] : 0),
    'crm_task_id'=>(isset($_POST['crm_task_id']) ? $_POST['crm_task_id'] : 0),
  ));
    
  
  $return = array('success' => true,'ask_user'=>false, 'message' => base64_encode('OK'), 'exist_rows' => array(), 'user_id' => intval($user_id),'gks_nickname' => trim_gks($gks_nickname),'balance_user_before' =>0);
  echo json_encode($return); die();    
  
}


$return = array('success' => false, 'message' => base64_encode('error command'));
echo json_encode($return); die();
