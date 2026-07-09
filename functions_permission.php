<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


$gks_permission_get_user_ret=array(); // to key einai to user_id

function gks_permission_user_must_login_page($fromupdate=false) {
  global $my_wp_user_id;
  if ($my_wp_user_id<=0) {
    header('Location: /wp-login.php?redirect_to='.urlencode('https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'])); 
    die();
  }
  if ($fromupdate==false) {
    global $GKS_LANG_DEFAULT;
    if ($GKS_LANG_DEFAULT=='')  {
      if (!(isset($_SERVER['SCRIPT_NAME']) and $_SERVER['SCRIPT_NAME']=='/my/admin-first-run.php')) {
        //echo '<pre>';print_r($_SERVER);die();
        header('Location: /my/admin-first-run.php'); 
        die();
      }
      
    }
  }
  gks_load_lang();
}

function gks_permission_user_must_login_post() {
  global $my_wp_user_id;
  //echo '<pre>';print_r($_SERVER);die();
  if ($my_wp_user_id<=0) {
    $redirectto='/wp-login.php?redirect_to='.urlencode('https://'.$_SERVER['SERVER_NAME'].'/my/'); 
    if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='') {
      $redirectto='/wp-login.php?redirect_to='.urlencode($_SERVER['HTTP_REFERER']); 
    }
    $return=array('success' => false,
      'message'=>base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση').'<br>'.gks_lang('Θα πρέπει να συνδεθείτε')),
      'user_not_login'=>true, 
      'redirectto'=>$redirectto,
    );
    debug_mail(false,$return['message'],'');
    echo json_encode($return); die();
  }
  gks_load_lang();
}


function gks_permission_get_user($user_id,$send_debug_email=true) {
  global $db_link;
  global $my_wp_user_info;
  global $gks_permission_get_user_ret;
  global $GKS_USERS_ACCESS_ROLES;
  
  if (isset($gks_permission_get_user_ret[$user_id])) return $gks_permission_get_user_ret[$user_id];
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/gks_permission_get_user'.rand(1000,9999).'.txt',print_r($gks_permission_get_user_ret,true));
  
  $return=array('success' => false,'message'=>'permission error','data'=>array());
  $return['data']['user_roles']=array();
  $return['data']['user_is_admin']=false;
  $return['data']['objects']=array();
  
  if ($user_id<=0) {
    $return['message']=gks_lang('Δεν έχετε συνδεθεί').'<br>'.gks_lang('Δεν επιτρέπεται η πρόσβαση');
    if ($send_debug_email) debug_mail(false,$return['message'],'');
    return $return;
  }
  //echo '<pre>'.$user_id;die();
  
  $sql="select meta_value from ".GKS_WP_TABLE_PREFIX."usermeta where user_id=".$user_id." and meta_key='".GKS_WP_TABLE_PREFIX."capabilities'";
  //echo '<pre>';echo $sql;die(); 
  $result = $db_link->query($sql);
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows!=1) {$return['message']=gks_lang('Δεν βρέθηκε η επαφή').'<br>'.gks_lang('Σίγουρα έχετε συνδεθεί ;');debug_mail(false,$return['message'],$sql);return $return;}  
  $row = $result->fetch_assoc();
  $user_roles=unserialize($row['meta_value']);
  
  $return['data']['user_roles']=$user_roles;
  if (isset($user_roles['administrator']) and $user_roles['administrator']=true) $return['data']['user_is_admin']=true;
  else if (isset($user_roles['adminmy']) and $user_roles['adminmy']=true) $return['data']['user_is_admin']=true;
  
  $has_access=false;
  if ($return['data']['user_is_admin']==false) {
    foreach ($user_roles as $key_role => $user_role) {
      if (in_array($key_role,$GKS_USERS_ACCESS_ROLES) and intval($user_role)!=0) {
        $has_access=true; 
        break; 
      }
    }
    //print '<pre>';print_r($GKS_USERS_ACCESS_ROLES);print_r($user_roles);die();
  }
  
    
  //echo $has_access;die();
  
  if ($return['data']['user_is_admin']==false and $has_access==false) {
    $return['success']=false;
    $return['message']=gks_lang('Δεν επιτρέπεται η πρόσβαση');
    //print '<pre>';print_r($return);die();
    $gks_permission_get_user_ret[$user_id]=$return;
    return $return;    
  } 

  $sql="SELECT gks_permission_user.*, gks_permission_object.table_name,gks_permission_object.object_name
  FROM gks_permission_user 
  LEFT JOIN gks_permission_object ON gks_permission_user.permission_object_id = gks_permission_object.id_permission_object
  WHERE gks_permission_user.user_id=".$user_id." AND gks_permission_object.id_permission_object Is Not Null
  ORDER BY gks_permission_object.sortorder,gks_permission_object.object_name";
  $result = $db_link->query($sql);
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  
  while ($row = $result->fetch_assoc()) {
    $perm_condition01=array(); if (trim_gks($row['perm_condition01'])!='') $perm_condition01=unserialize($row['perm_condition01']);
    $perm_condition02=array(); if (trim_gks($row['perm_condition02'])!='') $perm_condition02=unserialize($row['perm_condition02']);
    $perm_condition03=array(); if (trim_gks($row['perm_condition03'])!='') $perm_condition03=unserialize($row['perm_condition03']);
    $perm_condition04=array(); if (trim_gks($row['perm_condition04'])!='') $perm_condition04=unserialize($row['perm_condition04']);
    $perm_condition05=array(); if (trim_gks($row['perm_condition05'])!='') $perm_condition05=unserialize($row['perm_condition05']);
    $perm_condition06=array(); if (trim_gks($row['perm_condition06'])!='') $perm_condition06=unserialize($row['perm_condition06']);
    $perm_condition07=array(); if (trim_gks($row['perm_condition07'])!='') $perm_condition07=unserialize($row['perm_condition07']);
    $perm_condition08=array(); if (trim_gks($row['perm_condition08'])!='') $perm_condition08=unserialize($row['perm_condition08']);
    $perm_condition09=array(); if (trim_gks($row['perm_condition09'])!='') $perm_condition09=unserialize($row['perm_condition09']);
    $perm_condition10=array(); if (trim_gks($row['perm_condition10'])!='') $perm_condition10=unserialize($row['perm_condition10']);


    $perm_int_cond01=0; $perm_int_cond01=intval($row['perm_int_cond01']);
    $perm_int_cond02=0; $perm_int_cond02=intval($row['perm_int_cond02']);
    
    $return['data']['objects'][$row['table_name']] =array(
      'id_permission_object'=>$row['permission_object_id'],
      'table_name'=>$row['table_name'],
      'object_name'=>$row['object_name'],
      'view'=>$row['perm_view'],
      'edit'=>$row['perm_edit'],
      'add'=>$row['perm_add'],
      'delete'=>$row['perm_delete'],
      'autocomplete'=>$row['perm_autocomplete'],
      'condition01'=>$perm_condition01,
      'condition02'=>$perm_condition02,
      'condition03'=>$perm_condition03,
      'condition04'=>$perm_condition04,
      'condition05'=>$perm_condition05,
      'condition06'=>$perm_condition06,
      'condition07'=>$perm_condition07,
      'condition08'=>$perm_condition08,
      'condition09'=>$perm_condition09,
      'condition10'=>$perm_condition10,

      'int_cond01'=>$perm_int_cond01,
      'int_cond02'=>$perm_int_cond02,
    );
  }
  $return['success']=true;
  $return['message']='OK';
  //print '<pre>';print_r($return);die();
  //print '<pre>';print_r($return);die();
  $gks_permission_get_user_ret[$user_id]=$return;
  
  return $return;
}
function gks_permission_user_can_action($user_id, $table_name,$action,$id,$send_debug_email=true) {
  $return=array('success' => false,'message'=>'permission error');
  $perm_ret=gks_permission_get_user($user_id,$send_debug_email);
  
  //print '<pre>';print_r($perm_ret);die();
  
  if ($perm_ret['success']==false) {
    $return['message']=$perm_ret['message'];
    return $perm_ret;
  }
  
  
  //print '<pre>';print_r($perm_ret);die();
  
  if (count($perm_ret['data']['objects'])==0 and $perm_ret['data']['user_is_admin']) {
    //echo 'ffffffffffffff';die();
    //einai admin, xoris na exei oristhei kapoio dikaioma, opote ola kala
  } else {
  
    if (isset($perm_ret['data']['objects'][$table_name])==false or $perm_ret['data']['objects'][$table_name][$action]==0) {
      $return['message']=gks_lang('Δεν επιτρέπεται η').' <br><b>';
      if ($action=='view') $return['message'].=gks_lang('Προβολή');
      else if ($action=='edit') $return['message'].=gks_lang('Επεξεργασία');
      else if ($action=='add') $return['message'].=gks_lang('Προσθήκη');
      else if ($action=='delete') $return['message'].=gks_lang('Διαγραφή');
      else if ($action=='autocomplete') $return['message'].=gks_lang('Επιλογή');
      
      $return['message'].='</b><br>'.gks_lang('στο αντικείμενο').'<br>';
      if (isset($perm_ret['data']['objects'][$table_name]['object_name'])) {
        $return['message'].='<b>'.$perm_ret['data']['objects'][$table_name]['object_name'].'</b>';
      } else {
        $return['message'].='obj id: <b>'.$table_name.'</b>';
      }
      return $return;
    }
  }
  
  //print '<pre>';print_r($perm_ret);die();
  
  $return['success']=true;
  $return['message']='OK';
  return $return;
}

function gks_permission_user_can_action_javascript($user_id, $table_name,$action,$id) {
  $perm_ret=gks_permission_user_can_action($user_id, $table_name,$action,$id);
  if ($perm_ret['success']==false) return 'false'; else return 'true';
}
function gks_permission_user_can_action_php($user_id, $table_name,$action,$id) {
  $perm_ret=gks_permission_user_can_action($user_id, $table_name,$action,$id);
  if ($perm_ret['success']==false) return false; else return true;
}

function gks_permission_user_condition($user_id, $table_name,$condition) {
  $perm_ret=gks_permission_get_user($user_id);
  if ($perm_ret['success']==false) return array(-1);
  if (isset($perm_ret['data']['objects'][$table_name]['condition'.$condition])==false) return array();
  if (is_array($perm_ret['data']['objects'][$table_name]['condition'.$condition])==false) return array();
  //print '<pre>';print_r($perm_ret);die();
  return $perm_ret['data']['objects'][$table_name]['condition'.$condition];
}
function gks_permission_user_int_cond($user_id, $table_name,$condition) {
  $perm_ret=gks_permission_get_user($user_id);
  if ($perm_ret['success']==false) return 0;
  if (isset($perm_ret['data']['objects'][$table_name]['int_cond'.$condition])==false) return 0;
  //print '<pre>';print_r($perm_ret['data']['objects'][$table_name]);die();
  return $perm_ret['data']['objects'][$table_name]['int_cond'.$condition];
}


/*
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_country'];?>" data-model="gks_country" data-backurl="admin-country.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

*/
