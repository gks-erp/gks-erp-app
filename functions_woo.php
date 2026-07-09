<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_woo_get_eshop($id) {
  global $db_link;
  
    
  $sql="SELECT gks_eshops.*, gks_company_subs.company_sub_title
  FROM (gks_eshops
  LEFT JOIN gks_company ON gks_eshops.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_eshops.company_sub_id = gks_company_subs.id_company_sub
  where id_eshop=".$id;
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));} 
        

  if ($result->num_rows==0) {
    debug_mail(false,'eshop not found',$sql);
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το eshop με ID').':'.$id));} 
  
  $row = $result->fetch_assoc(); 
  
  if (intval($row['eshop_disable'])!=0) {
    debug_mail(false,'eshop_disable is disabled',$sql);
    return array('success' => false, 'message' => base64_encode(str_replace('[1]',$row['eshop_name'],gks_lang('Το eshop <b>[1]</b> δεν είναι ενεργό'))));} 
  if (empty($row['eshop_key'])) {
    debug_mail(false,'eshop_key not set',$sql);
    return array('success' => false, 'message' => base64_encode(str_replace('[1]',$row['eshop_name'],gks_lang('Στο eshop <b>[1]</b> δεν έχει ορισθεί το κλειδί'))));} 
  if (empty($row['eshop_url'])) {
    debug_mail(false,'eshop_url not set',$sql);
    return array('success' => false, 'message' => base64_encode(str_replace('[1]',$row['eshop_name'],gks_lang('Στο eshop <b>[1]</b> δεν έχει ορισθεί το URL'))));} 
  
  $row['tax_class_basikos']=trim_gks($row['tax_class_basikos']);
  $row['tax_class_meiomenos']=trim_gks($row['tax_class_meiomenos']);
  $row['tax_class_ypermeiomenos']=trim_gks($row['tax_class_ypermeiomenos']);
  $row['tax_class_yperypermeiomenos']=trim_gks($row['tax_class_yperypermeiomenos']);
  $row['tax_class_xorisfpa']=trim_gks($row['tax_class_xorisfpa']);
  
  $row['eshop_url_page']=$row['eshop_url'].'/wp-content/plugins/gks_core/api-gks.php';
  $row['hash_time']=time();
  $row['hash_key']=md5($row['hash_time'].'gks'.$row['eshop_key'].'gkshash'.$row['hash_time']);
  
  
  $sql_list="SELECT id_delivery_method, delivery_method_name FROM gks_delivery_methods WHERE delivery_method_disabled=0 order by mysortorder";
  $result_list = $db_link->query($sql_list);
  if (!$result_list) {
    debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));} 
  $myarray=array();
  while ($row_list= $result_list->fetch_assoc()) {
    $myarray[]=array(
      'g' => $row_list['id_delivery_method'],
      'w' => $row_list['delivery_method_name'],
      'wt' => $row_list['delivery_method_name'],
    );
  }
  $temp=trim_gks($row['woo_delivery_to_gks']);
  if ($temp!='') {
    $temp=unserialize($temp);
    foreach ($temp as $value) {
      $myarray[]=$value;
    } 
  }
  $row['woo_delivery_to_gks']=$myarray;
  


  $sql_list="SELECT id_payment_acquirer, payment_acquirer_name FROM gks_payment_acquirers WHERE payment_acquirer_disabled=0 order by mysortorder";
  $result_list = $db_link->query($sql_list);
  if (!$result_list) {
    debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));} 
  $myarray=array();
  while ($row_list= $result_list->fetch_assoc()) {
    $myarray[]=array(
      'g' => $row_list['id_payment_acquirer'],
      'w' => $row_list['payment_acquirer_name'],
      'wt' => $row_list['payment_acquirer_name'],
    );
  }
  $temp=trim_gks($row['woo_payment_to_gks']);
  if ($temp!='') {
    $temp=unserialize($temp);
    foreach ($temp as $value) {
      $myarray[]=$value;
    } 
  }
  $row['woo_payment_to_gks']=$myarray;
  

  
  $temp=trim_gks($row['update_state_gks_transfer']);
  $row['update_state_gks_transfer']=array();
  if ($temp!='') $row['update_state_gks_transfer']=explode(',',$temp);

  $temp=trim_gks($row['update_state_gks_reservation']);
  $row['update_state_gks_reservation']=array();
  if ($temp!='') $row['update_state_gks_reservation']=explode(',',$temp);

  $temp=trim_gks($row['update_state_gks_order']);
  $row['update_state_gks_order']=array();
  if ($temp!='') $row['update_state_gks_order']=explode(',',$temp);

  $temp=trim_gks($row['update_state_gks_acc_inv']);
  $row['update_state_gks_acc_inv']=array();
  if ($temp!='') $row['update_state_gks_acc_inv']=explode(',',$temp);

  $temp=trim_gks($row['update_state_woo']);
  $row['update_state_woo']=array();
  if ($temp!='') $row['update_state_woo']=explode(',',$temp);
  

  
  
  return array('success' => true, 'message' => base64_encode('OK'),'eshop' => $row);  
   
}


function gks_woo_post($eshop, $data) {

  //print_r($data); die();
  
  $data['key']=$eshop['hash_key'];
	$data['time']=$eshop['hash_time'];

  $data['wpml_enable']=0;        if (isset($eshop['wpml_enable'])) $data['wpml_enable']=$eshop['wpml_enable'];
  if ($data['wpml_enable']==1) {
    $data['wpml_default_lang']=''; if (isset($eshop['wpml_default_lang'])) $data['wpml_default_lang']=$eshop['wpml_default_lang'];
    $data['wpml_languages']=[];
    
    
    
    if (isset($eshop['wpml_languages'])) {
      $wpml_languages=unserialize($eshop['wpml_languages']);
      //echo '<pre>';print_r($wpml_languages);die();
      $temp=[];
      foreach ($wpml_languages as $value) {
        $temp[]=$value['language_code'];
      }
      if (count($temp)>0) {
        $data['wpml_languages']=$temp;
      }
    }
    $data['wpml_default_lang_code']=''; if (isset($eshop['wpml_default_lang_code'])) $data['wpml_default_lang_code']=$eshop['wpml_default_lang_code'];
  }

  $data_string = json_encode($data);
  //print_r($data_string);die();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $eshop['eshop_url_page']);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
      array(
          'accept: application/json',
          'Content-Type: application/json',
          //'Content-Type: application/x-www-form-urlencoded',
          'Content-Length: ' . strlen($data_string)
      )
  ); 
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch); 

  //echo '<pre>';echo $result."\n";die();
  //echo '<pre>';echo $result."\n";echo 'error number:';var_dump($gks_curl_errno);var_dump($gks_curl_info);
  
  //echo $result;die();
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  if ($gks_curl_http_code==0) { //HTTP Host not found
    debug_mail(false,'gks_woo_post error',                      gks_lang('Δεν βρέθηκε ο διακομιστής του eshop').'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο διακομιστής του eshop')));
    
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'gks_woo_post error',                      gks_lang('Δεν βρέθηκε το πρόσθετο στο Wordpress').'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πρόσθετο στο Wordpress')));

  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    debug_mail(false,'gks_woo_post error',                      gks_lang('Γενικό σφάλμα').' (1)'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα').' (1)'));
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    debug_mail(false,'gks_woo_post error',                      gks_lang('Δεν επιτρέπεται η πρόσβαση').'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang(gks_lang('Δεν επιτρέπεται η πρόσβαση'))));

  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'gks_woo_post error',                      gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error: '.$gks_curl_http_code));

  }

  //echo $ret['message']; die();
  $parts=explode("\r\n\r\n",$result,2);
  if (count($parts)!=2) {
    debug_mail(false,'gks_woo_post result error',$result);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (1).'.$result));}
  
  $response=trim_gks($parts[1]);
  if ($response=='') {
    debug_mail(false,'gks_woo_post response error',$response);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (2).'.$result));}
    
  
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'gks_woo_post json_decode error',$response);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (3).'.$result));}
      
  return array('success' => true, 'message' => base64_encode('OK'), 'response_array' => $response_array);

}

function gks_woo_product_update_local_from_woo($eshop, $data, $woo_settings) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;

  //ini_set('max_execution_time', 600);
  //set_time_limit(600);  

  //echo '<pre>';print_r($eshop);die();
  //echo '<pre>';print_r($data);die();
  //echo '<pre>';print_r($woo_settings);die();
  
  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';
  
  
  $id_product=0;
  $eshop_id=$eshop['id_eshop'];
  $remote_product_id=$data['id'];
  $is_new=false;
  $product_class_new='simple';
  if ($data['type']=='variable') $product_class_new='variable';
  
  $remote_lang='';if (isset($data['wpml_post_lang']['language_code'])) $remote_lang=$data['wpml_post_lang']['language_code'];
  

  //echo '<pre>bbbb';die();
  $sql="select id_woo_product,product_id,remote_lang from gks_woo_product where remote_product_id=".$remote_product_id." and eshop_id=".$eshop_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $id_product=$row['product_id'];
    $id_woo_product=$row['id_woo_product'];
    $remote_lang_db=trim_gks($row['remote_lang']);
    //echo '<pre>'.$remote_lang.'|'.$sql;die();
    if ($remote_lang!='' and $remote_lang!=$remote_lang_db) {
      $sql="update gks_woo_product set remote_lang='".$db_link->escape_string($remote_lang)."' where id_woo_product=".$id_woo_product;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
        
    }
    
  } else {
    
    
    if ($eshop['wpml_enable']==1 and $remote_lang!='' and $eshop['wpml_default_lang_code']!=$remote_lang) {
      //other default lang    
      // kai den iparxei i antistixi eggrafi ston gks_woo_product giati molis prostethike i metafrasi
      // ara prepei na vro gia poio proion einai i metafrasi
      //prepei na vro to proion me tin deault glossa
      $has_done_this=false;
      foreach ($data['wpml_other_lang_ids'] as $lang_item) {
        if ($lang_item['lang']==$eshop['wpml_default_lang_code']) {
          //echo '<pre>111111 ';print_r($data);die();
          $def_lang_product_id=$lang_item['product_id'];
          $sql="select * from gks_woo_product where remote_product_id=".$def_lang_product_id." and eshop_id=".$eshop_id;
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          if ($result->num_rows>=1) {
            $row = $result->fetch_assoc();          
            $id_product=$row['product_id'];
            
            $sql="insert into gks_woo_product (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              product_id,eshop_id,remote_product_id,
              remote_lang
            ) values (
              now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
              ".$id_product.",".$eshop_id.",".$remote_product_id.",
              '".$db_link->escape_string($remote_lang)."'
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));} 
            $has_done_this=true;
            
            //echo '<pre>111111 '.$id_product.' |';print_r($data);die();
          }
          
          
        }
        
         // loop through values 
      } 
      
      if ($has_done_this==false) {
        debug_mail(false,'error find default lang parent',print_r($data,true));
        return array('success' => false, 'message' => base64_encode('error find default lang parent'));}        
        
    
    } else {
      $sql="insert into gks_eshop_products (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_class
      ) values (
        '".$db_link->escape_string($data['date_created'])."','".$db_link->escape_string($data['date_modified'])."',".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        '".$product_class_new."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      $id_product=$db_link->insert_id;
      $is_new=true;
      
      
      $sql="insert into gks_woo_product (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_id,eshop_id,remote_product_id,
        remote_lang
      ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$id_product.",".$eshop_id.",".$remote_product_id.",
        '".$db_link->escape_string($remote_lang)."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));} 
      $id_woo_product=$db_link->insert_id;
    }
  }
  
  $remote_product_is_in_def_lang=true;
  if ($eshop['wpml_enable']==1 and $remote_lang!='' and $eshop['wpml_default_lang_code']!=$remote_lang) {
    //other default lang
    $remote_product_is_in_def_lang=false;
    
    //echo '<pre>id_product: '.$id_product.'|'.$remote_lang.'|'.$eshop['wpml_default_lang_code'].'|'.$remote_product_is_in_def_lang.'|';die();
  } else {
    // default lang
  
  
    if ($eshop['wpml_enable']==1 and isset($data['wpml_other_lang_ids'])) {
      foreach ($data['wpml_other_lang_ids'] as $lang_item) {
        if ($lang_item['lang']!='' and $lang_item['lang']!=$remote_lang && $lang_item['product_id']!=$remote_product_id) {
          
          $sql="select * from gks_woo_product where remote_product_id=".$lang_item['product_id']." and eshop_id=".$eshop_id;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          if ($result->num_rows>=1) { 
            $row = $result->fetch_assoc();
            
            if (intval($row['product_id'])!=$id_product or trim_gks($row['remote_lang'])!=$lang_item['lang']) {
              $sql="update gks_woo_product set
              product_id=".$id_product.",
              remote_lang='".$db_link->escape_string($lang_item['lang'])."'
              where id_woo_product=".$row['id_woo_product'];
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));} 
              
              //echo 'hhhhhhhhh '; die();
              
            }
            
          } else {
            $sql="insert into gks_woo_product (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              product_id,eshop_id,remote_product_id,
              remote_lang
            ) values (
              now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
              ".$id_product.",".$eshop_id.",".$lang_item['product_id'].",
              '".$db_link->escape_string($lang_item['lang'])."'
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));} 
            //echo 'iiii ';
          }       
          
          
          //echo 'ffff '.$lang_item['lang'].' '.$sql;die();
          
        }
        
      }
      
      
      
    
    }
  }
  //echo 'hhhhhhhhhhhh';die();
  
  $sql="select * from gks_eshop_products where id_product=".$id_product;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}  
  
  $local = $result->fetch_assoc();
  $product_class_old=$local['product_class'];
  $product_base_type=$local['product_base_type'];
  $product_can_sell=$local['product_can_sell'];
  $product_can_buy=$local['product_can_buy'];
  $exist_product_price=floatval($local['product_price']);
  $exist_product_price_sale=floatval($local['product_price_sale']);

  
  
  $will_update=false;
  $update_GKS_IDIOTITES_CACHE_VER=false;
  if ($is_new or strtotime($local['mydate_edit']) < strtotime($data['date_modified'])) $will_update=true;
  
  $will_update=true;
  
  if ($will_update) {
    //echo 'will_update:' .$will_update."\n";
    if ($product_class_old=='variable' and $product_class_new!='variable') {
      $sql="update gks_eshop_products set 
      product_disable=1,
      product_parent_id=0,
      product_parent_old_id=".$id_product."
      where product_parent_id=".$id_product;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
    }
    
    
    if ($remote_product_is_in_def_lang) {
      
      $sql="update gks_eshop_products set ";
      if ($is_new) {
        $product_can_buy=0;
        if ($data['status']=='publish') {// publish pending (gia elegxo) draft
          $product_can_sell=1;
        } else {
          $product_can_sell=0;
        }
      }
      
      if ($data['gks_woo_product_files_upload']) { 
        if ($data['gks_woo_product_files_upload_enable']=='yes') {
          $product_base_type=1;
        } else {
          if ($is_new) $product_base_type=0;
        }
      } else {
        if ($is_new) $product_base_type=0;
      }
      
      $sql.="product_base_type=".$product_base_type.",
      product_can_sell=".$product_can_sell.",
      product_can_buy=".$product_can_buy.",
      
      product_class='".$product_class_new."',
      product_sku='".$db_link->escape_string($data['sku'])."',
      product_gtin='".$db_link->escape_string($data['gtin'])."',
      product_descr='".$db_link->escape_string($data['name'])."',
      product_descr_small='".$db_link->escape_string($data['short_description'])."',
      product_descr_big='".$db_link->escape_string($data['description'])."',";
      
      if (intval($data['virtual'])==0) {
        $sql.="product_need_apostoli=1,";
      } else {
        $sql.="product_need_apostoli=0,";
      }
      if (floatval($data['regular_price'])!=0) {
        $sql.="product_price_retail=".number_format(floatval($data['regular_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
      }
      $sql.="product_price_retail_sale=".number_format(floatval($data['sale_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
      
      if (isset($data['date_on_sale_from']) and trim_gks($data['date_on_sale_from'])!='') 
        $sql.="product_price_retail_sale_from='".$db_link->escape_string($data['date_on_sale_from'])."',";
      if (isset($data['date_on_sale_to']) and trim_gks($data['date_on_sale_to'])!='') 
        $sql.="product_price_retail_sale_to='".$db_link->escape_string($data['date_on_sale_to'])."',";
      
      $update_xondriki_price=false;
      if ($exist_product_price==0 and $exist_product_price_sale==0) {
        $sql.="product_price=".number_format(floatval($data['regular_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
        $sql.="product_price_sale=".number_format(floatval($data['sale_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
        $update_xondriki_price=true;
        
        //product_price_retail_include_vat
      }
      
      if ($woo_settings['weight_unit']=='kg') {
        $val=floatval($data['weight'])*1000;
        $sql.="product_varos=".number_format($val,3,'.','').",";
      } else if ($woo_settings['weight_unit']=='g') {
        $val=floatval($data['weight']);
        $sql.="product_varos=".number_format($val,3,'.','').",";
      } else if ($woo_settings['weight_unit']=='lbs') {
        $val=floatval($data['weight'])*0.453592*1000;
        $sql.="product_varos=".number_format($val,3,'.','').",";
      } else if ($woo_settings['weight_unit']=='oz') {
        $val=floatval($data['weight'])*0.0283495*1000;
        $sql.="product_varos=".number_format($val,3,'.','').",";
      } else {
        $val=floatval($data['weight']);
        $sql.="product_varos=".number_format($val,3,'.','').",";
      }
    
      if ($woo_settings['dimension_unit']=='m') { //m cm mm in yd
        $sql.="product_ogos_x=".number_format(floatval($data['length'])*100,3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width'])*100,3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height'])*100,3,'.','').",";
      } else if ($woo_settings['dimension_unit']=='cm') { 
        $sql.="product_ogos_x=".number_format(floatval($data['length']),3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width']),3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height']),3,'.','').",";
      } else if ($woo_settings['dimension_unit']=='mm') { 
        $sql.="product_ogos_x=".number_format(floatval($data['length'])/10,3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width'])/10,3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height'])/10,3,'.','').",";
      } else if ($woo_settings['dimension_unit']=='in') { 
        $sql.="product_ogos_x=".number_format(floatval($data['length'])*2.54,3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width'])*2.54,3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height'])*2.54,3,'.','').",";
      } else if ($woo_settings['dimension_unit']=='yd') { 
        $sql.="product_ogos_x=".number_format(floatval($data['length'])*91.44,3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width'])*91.44,3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height'])*91.44,3,'.','').",";
      } else {
        $sql.="product_ogos_x=".number_format(floatval($data['length']),3,'.','').",";
        $sql.="product_ogos_y=".number_format(floatval($data['width']),3,'.','').",";
        $sql.="product_ogos_z=".number_format(floatval($data['height']),3,'.','').",";
        
      }
      
      $product_fpa_base_id=0;  //	keno
      if ($woo_settings['calc_taxes']=='yes') {
        $sql.="product_price_retail_include_vat=".($woo_settings['prices_include_tax']=='yes' ? 1 : 0).",";
        if ($update_xondriki_price) $sql.="product_price_include_vat=".($woo_settings['prices_include_tax']=='yes' ? 1 : 0).",";
        
        if ($data['tax_status']!='taxable') { //taxable none shipping
          $product_fpa_base_id=1004; //	No FPA
        } else {
          $product_fpa_base_id=1001;  //	Kanonikos FPA
          if ($data['tax_class']!='') {
            foreach ($woo_settings['wootaxes'] as $wtax) {
              if ($data['tax_class'] == $wtax['slug']) {
  //              echo $wtax['slug']."\n";
  //              echo $wtax['name']."\n";
  //              echo $eshop['tax_class_basikos']."\n";
  //              echo $eshop['tax_class_meiomenos']."\n";
  //              echo $eshop['tax_class_ypermeiomenos']."\n";
  //              echo $eshop['tax_class_xorisfpa']."\n";
                
                if ($eshop['tax_class_basikos']!='' and $wtax['name']==$eshop['tax_class_basikos']) $product_fpa_base_id=1001; //	Kanonikos
                else if ($eshop['tax_class_meiomenos']!='' and $wtax['name']==$eshop['tax_class_meiomenos']) $product_fpa_base_id=1002; //	Meiomenos
                else if ($eshop['tax_class_ypermeiomenos']!='' and $wtax['name']==$eshop['tax_class_ypermeiomenos']) $product_fpa_base_id=1003; //	Ypermeiomenos
                else if ($eshop['tax_class_yperypermeiomenos']!='' and $wtax['name']==$eshop['tax_class_yperypermeiomenos']) $product_fpa_base_id=1005; //	Yper-Ypermeiomenos
                else if ($eshop['tax_class_xorisfpa']!='' and $wtax['name']==$eshop['tax_class_xorisfpa'])  $product_fpa_base_id=1004; //	No FPA
                break; 
              } 
            }
          }  
        }
      } else {
        $sql.="product_price_retail_include_vat=1,";
        if ($update_xondriki_price) $sql.="product_price_include_vat=1,";
      }
      $sql.="product_fpa_base_id=".$product_fpa_base_id.",";
    
      if ($data['gks_woo_product_files_upload']) {
        if ($data['gks_woo_product_files_upload_enable']=='yes' and $data['gks_woo_product_files_upload_required']=='yes') {
          $sql.='product_need_multi_files=1,';
        } else {
          $sql.='product_need_multi_files=0,';
        }
        $sql.='product_need_multi_files_min='.intval($data['gks_woo_product_files_upload_file_min']).',';
        $sql.='product_need_multi_files_max='.intval($data['gks_woo_product_files_upload_file_max']).',';
        
      }
    
      
      
      // oxi to mydate_edit=now(),
      $sql.="
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_product=".$id_product." limit 1";
      //mydate_edit='".$db_link->escape_string($data['date_modified'])."',
      
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      
      $images=array();
      if ($data['image_id']>0 and $data['image_id_url']!='' and $data['image_id_path']!='' and $data['image_id_filesize']>0 and $data['image_id_filemtime']>0) {
        $images[]=array(
          'default'=>true,
          'id'=>$data['image_id'],
          'url'=>$data['image_id_url'],
          'path'=>$data['image_id_path'],
          'filesize'=>$data['image_id_filesize'],
          'filemtime'=>$data['image_id_filemtime'],
        );
      }
      if (isset($data['gallery_image_ids_array'])) {
        foreach ($data['gallery_image_ids_array'] as $value) {
          if ($value['id']>0 and $value['url']!='' and $value['path']!='' and $value['filesize']>0 and $value['filemtime']>0) {
            
            $image_found=false;
            foreach ($images as $myimage) {
              if ($myimage['id']==$value['id']) {
                $image_found=true;
                break; 
              }
            }
            if ($image_found==false) {
            
              $images[]=array(
                'default'=>false,
                'id'=>$value['id'],
                'url'=>$value['url'],
                'path'=>$value['path'],
                'filesize'=>$value['filesize'],
                'filemtime'=>$value['filemtime'],
              );
            }
          }
        }  
      }
      
      //print '<pre>bbbbbbbbbbbbb ';print_r($data);die();
      
      if (isset($data['variations']) and is_array($data['variations']) and count($data['variations'])>0) {
        foreach ($data['variations'] as $key_myv => $myvariation) {
          if ($myvariation['image_id']>0 and $myvariation['image_id_url']!='' and $myvariation['image_id_path']!='' and $myvariation['image_id_filesize']>0 and $myvariation['image_id_filemtime']>0) {
            $image_id=$myvariation['image_id'];
            $image_found=false;
            foreach ($images as $myimage) {
              if ($myimage['id']==$image_id) {
                $image_found=true;
                break; 
              }
            }
            if ($image_found==false) {
              $images[]=array(
                'default'=>false,
                'id'=>$myvariation['image_id'],
                'url'=>$myvariation['image_id_url'],
                'path'=>$myvariation['image_id_path'],
                'filesize'=>$myvariation['image_id_filesize'],
                'filemtime'=>$myvariation['image_id_filemtime'],
              );        
            }
          }
        } 
      }
      
      
      if (count($images)>0) {
        $sql="SELECT * FROM gks_eshop_products_photo where product_id=".$id_product;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $exist_images=array();
        while ($row= $result->fetch_assoc()) { 
          $exist_images[]=$row;
        }     
        //print_r($exist_images);
        
        $dir_url='/my/uploads/products/'.$id_product.'/'.date('Y/m/d').'/';
        $upload_dir = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$dir_url;
        
        foreach ($images as $index_image => $myimg) {
          $will_download=true;
          $images[$index_image]['local_ee_url']='';
          foreach ($exist_images as $ee) {
            if (mb_basename($ee['photo_url']) == mb_basename($myimg['url']) and $ee['mysize']==$myimg['filesize']) {
              $images[$index_image]['local_ee_url']=$ee['photo_url'];
              $will_download=false; break;
            }
            if ($ee['mysize']==$myimg['filesize'] and strtotime($ee['mydate'])==$myimg['filemtime']) {
              $images[$index_image]['local_ee_url']=$ee['photo_url'];
              $will_download=false; break;
            }
          }
          $images[$index_image]['will_download']=$will_download;
          if ($will_download==false) {
            $filename=mb_basename($myimg['path']);
            $thumb_url = $dir_url.'thumbnail/'.$filename;
            $images[$index_image]['thumb_url']=$thumb_url;
          } else {
            $filename=mb_basename($myimg['path']);
            do {
              if (file_exists($upload_dir.$filename)==false) break;
              $path_parts = pathinfo($myimg['path']);
              $filename=$path_parts['filename'].'_'.rand(10000,99999).'.'.$path_parts['extension'];
            } while(true);
            $savepath=$upload_dir.$filename;
            $photo_url=$dir_url.$filename;
            
            
            if (file_exists($upload_dir) == false) {
              if (@mkdir($upload_dir , 0755, true) == false ) {
                debug_mail(false,'can not create dir: ',$upload_dir);
                return array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος των φωτογραφιών').'<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));}  
            }
            if (file_exists($upload_dir.'thumbnail') == false) {
              if (@mkdir($upload_dir.'thumbnail' , 0755, true) == false ) {
                debug_mail(false,'can not create dir: ',$upload_dir.'thumbnail');
                return array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος thumbnail των φωτογραφιών').'<br>'.gks_lang('Ξαναδοκιμάστε αργότερα')));}  
            }
            
            
            $opts = array(
              'http'=>array(
                'timeout' => 300,  //600 Seconds  -> 10 mins
                'method'=>"GET",
                'header'=>"Accept-language: en" //\r\n
              ),
              'ssl'=>array(
                'verify_peer'=>false,
                'verify_peer_name'=>false,
              ),
            );
            
            $context = stream_context_create($opts);
            
    
            $file = @file_get_contents($myimg['url'], false, $context);
            if ($file != '') {
              $save = file_put_contents($savepath, $file);
              if (file_exists($savepath)) {
                $mysize=filesize($savepath);
                $sql="insert into gks_eshop_products_photo (
                   product_id,photo_url,mydate,mysize,ip,user_add_id
                ) values (
                  ".$id_product.",'".$db_link->escape_string($photo_url)."','".date('Y-m-d H:i:s',$myimg['filemtime'])."',
                  ".$mysize.",'".$db_link->escape_string($gkIP)."',".$my_wp_user_id."
                )";
                $result = $db_link->query($sql);
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
                
                
                
                $thumbpath = $upload_dir.'thumbnail/'.$filename;
                $thumb_url = $dir_url.'thumbnail/'.$filename;
                makeThumbnails_square($savepath,$thumbpath,300,false);
                //echo $savepath."\n";
                //echo $thumbpath;
                //die();
                $images[$index_image]['thumb_url']=$thumb_url;
                
  
                
                if ($mysize!=$myimg['filesize']) {
                  debug_mail(false,'warning filesize '.$mysize.'!='.$myimg['filesize'],print_r($myimg,true)."\n".$savepath);
                }
              } else {
                debug_mail(false,'file not saved',print_r($myimg,true)."\n".$savepath);
                return array('success' => false, 'message' => base64_encode('file not saved'.print_r($myimg,true)."\n".$savepath));
              }
    
              
            } else {
              debug_mail(false,'can not download file',print_r($myimg,true)."\n".$savepath);
              return array('success' => false, 'message' => base64_encode('can not download file'.print_r($myimg,true)."\n".$savepath));
            }
            
            
          }
          
            
                 
        } 
      }
      
      $found_default=false;
      foreach ($images as $index_image => $myimg) {
        if ($myimg['default']) {
          $found_default=true;
          if ($myimg['local_ee_url']!='') {
            $filename=mb_basename($myimg['local_ee_url']);
            $thumb_url = dirname($myimg['local_ee_url']).'/thumbnail/'.$filename;
            $sql="update gks_eshop_products set product_photo='".$db_link->escape_string($thumb_url)."' where id_product=".$id_product;  
          } else {
            $sql="update gks_eshop_products set product_photo='".$db_link->escape_string($myimg['thumb_url'])."' where id_product=".$id_product; 
          }
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
        }
      }
      if ($found_default==false) {
        $sql="update gks_eshop_products set product_photo='' where id_product=".$id_product;  
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
      }
      //print '<pre>';print_r($images);die();
      
      
      
      
      $not_delete_idiotites=array();
      if (isset($data['attributes']) and count($data['attributes'])>0) {
        //global attributes
        foreach ($data['attributes'] as $slug => $attribute) {
          //echo '<pre>';print_r($attribute); die();
          $label=rawurldecode(trim_gks($attribute['label']));
          if ($label!='') {
            $atrr_type=trim_gks($attribute['type']);
            $atrr_type_gks='10button';
            if ($atrr_type=='avada_button') $atrr_type_gks='10button';
            else if ($atrr_type=='avada_color') $atrr_type_gks='20color';
            else if ($atrr_type=='avada_image') $atrr_type_gks='30image';
            
            
            $sql="select id_product_idiotita from gks_product_idiotites where idiotita_name like '".$db_link->escape_string($label)."'";
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            if ($result->num_rows==0) {
              $sql="insert into gks_product_idiotites (
                mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                idiotita_name,idiotita_descr,idiotita_type
              ) values (
                now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                '".$db_link->escape_string($label)."',
                '',
                '".$atrr_type_gks."'
              )";
              $result = $db_link->query($sql);
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              $data['attributes'][$slug]['id_product_idiotita']=$db_link->insert_id;
              $update_GKS_IDIOTITES_CACHE_VER=true;
            } else {
              $row = $result->fetch_assoc();
              $data['attributes'][$slug]['id_product_idiotita']=$row['id_product_idiotita'];
            }
            //echo '<pre>'.$id_product_idiotita; die();
            
            
            foreach ($attribute['terms'] as $a_index => $attr_term) {
              $attr_name=trim_gks($attr_term['name']);
              if ($attr_name!='') {
                //echo '<pre>';print_r($attr_term);die();
                $sql="select id_product_idiotita_term,idiotita_term_color from gks_product_idiotites_terms
                where idiotita_id=".$data['attributes'][$slug]['id_product_idiotita']." 
                and idiotita_term_name like '".$db_link->escape_string($attr_name)."'";
                $result = $db_link->query($sql);
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
                if ($result->num_rows==0) {
                  $sql="insert into gks_product_idiotites_terms (
                    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                    idiotita_id,
                    idiotita_term_name,
                    idiotita_term_descr,
                    idiotita_term_color
                  ) values (
                    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                    ".$data['attributes'][$slug]['id_product_idiotita'].",
                    '".$db_link->escape_string($attr_name)."',
                    '".$db_link->escape_string(trim_gks($attr_term['description']))."',
                    '".$db_link->escape_string(trim_gks($attr_term['attribute_color_avada']))."'
                  )";
                  $result = $db_link->query($sql);
                  if (!$result) {
                    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                    return array('success' => false, 'message' => base64_encode('sql error'));}  
                  $data['attributes'][$slug]['terms'][$a_index]['id_product_idiotita_term']=$db_link->insert_id;
                  $update_GKS_IDIOTITES_CACHE_VER=true;
                } else {
                  $row = $result->fetch_assoc();
                  $data['attributes'][$slug]['terms'][$a_index]['id_product_idiotita_term']=$row['id_product_idiotita_term'];
                  if (trim_gks($row['idiotita_term_color'])!=trim_gks($attr_term['attribute_color_avada'])) {
                    $sql="update gks_product_idiotites_terms set 
                    idiotita_term_color='".$db_link->escape_string(trim_gks($attr_term['attribute_color_avada']))."'
                    where id_product_idiotita_term=".$row['id_product_idiotita_term'];
                    $result = $db_link->query($sql);
                    if (!$result) {
                      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                      return array('success' => false, 'message' => base64_encode('sql error'));}  
                  }
                }
                //echo '<pre>'.$id_product_idiotita_term; die();
              }
            
            
            
            }
            
            
          }
        }
        
        //product attributes
        
        foreach ($data['attributes'] as $slug => $attribute) {
          if (isset($attribute['id_product_idiotita']) and $attribute['id_product_idiotita']>0) {
            $not_delete_idiotites[]=$attribute['id_product_idiotita'];
            $sql="select id_eshop_products_idiotites,idiotita_is_variable from gks_eshop_products_idiotites 
            where product_id=".$id_product." and product_idiotita_id=".$attribute['id_product_idiotita'];
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            if ($result->num_rows==0) {
              $sql="insert into gks_eshop_products_idiotites (
                mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                product_id,product_idiotita_id,idiotita_is_variable
              ) values (
                now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                ".$id_product.",
                ".$attribute['id_product_idiotita'].",
                ".($attribute['variation']? '1':'0')."
              )";
              $result = $db_link->query($sql);
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              $id_eshop_products_idiotites=$db_link->insert_id;
            } else {
              $row = $result->fetch_assoc();
              $id_eshop_products_idiotites=$row['id_eshop_products_idiotites'];
              if ($row['idiotita_is_variable']!=($attribute['variation']? 1:0)) {
                $sql="update gks_eshop_products_idiotites set 
                idiotita_is_variable=".($attribute['variation']? '1':'0').",
                mydate_edit=now(),
                user_id_edit=".$my_wp_user_id.",
                myip='".$db_link->escape_string($gkIP)."'
                where id_eshop_products_idiotites=".$id_eshop_products_idiotites;
                $result = $db_link->query($sql);
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
              } 
            }
            
            //echo '<pre>';print $id_eshop_products_idiotites; die();
            $not_delete=array();
            foreach ($attribute['terms'] as $a_index => $attr_term) {
              if (isset($attr_term['id_product_idiotita_term']) and $attr_term['id_product_idiotita_term']>0) {
                $not_delete[]=$attr_term['id_product_idiotita_term'];
                $sql="select * from gks_eshop_products_idiotites_terms 
                where eshop_products_idiotites_id=".$id_eshop_products_idiotites."
                and product_idiotita_term_id=".$attr_term['id_product_idiotita_term'];
                $result = $db_link->query($sql);
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
                if ($result->num_rows==0) {
                  $sql="insert into gks_eshop_products_idiotites_terms (
                    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                    eshop_products_idiotites_id,product_idiotita_term_id
                  ) values (
                    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                    ".$id_eshop_products_idiotites.",".$attr_term['id_product_idiotita_term']."
                  )";
                  $result = $db_link->query($sql);
                  if (!$result) {
                    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                    return array('success' => false, 'message' => base64_encode('sql error'));}  
                }
              }
            }
            
            $sql="delete from gks_eshop_products_idiotites_terms where eshop_products_idiotites_id=".$id_eshop_products_idiotites;
            if (count($not_delete)>0) $sql.=" and product_idiotita_term_id not in (".implode(',',$not_delete).")";
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            
          }
          //echo '<pre>';print_r($data['attributes']); die();
        }
      }
      $sql="delete from gks_eshop_products_idiotites where product_id=".$id_product;
      if (count($not_delete_idiotites)>0) $sql.=" and product_idiotita_id not in (".implode(',',$not_delete_idiotites).")";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      
      $sql="DELETE FROM gks_eshop_products_idiotites_terms
      where eshop_products_idiotites_id not in (
        select id_eshop_products_idiotites from gks_eshop_products_idiotites
      )";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
    
    
      if (isset($data['variations']) and is_array($data['variations'])) {
        
        
        $sql="SELECT id_product, product_parent_id, product_parent_old_id
        FROM gks_eshop_products
        WHERE product_class='variable_item' 
        and product_parent_id=".$id_product." or product_parent_old_id=".$id_product;
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $exist_variations=array();
        $temp_ids=array();
        while ($row= $result->fetch_assoc()) {
          $row['product_idiotita_term_id']=array();
          $row['eshop_product_id']=0;
          $exist_variations[$row['id_product']]=$row;
          $temp_ids[]=$row['id_product'];
        }
        if (count($temp_ids)>0) {
          $sql="select product_id,product_idiotita_term_id from gks_eshop_products_variables where product_id in (".implode(',',$temp_ids).") and product_idiotita_term_id>0";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          while ($row= $result->fetch_assoc()) {
            if (isset($exist_variations[$row['product_id']])) {
              $exist_variations[$row['product_id']]['product_idiotita_term_id'][]=$row['product_idiotita_term_id'];
            }
          }
        }
        //echo '<pre>';print_r($exist_variations);die();
        
        
        foreach ($data['variations'] as $key_myv => $myvariation) {
          //echo '<pre>';print_r($myvariation);die();
          $data['variations'][$key_myv]['id_product']=0;
          if (isset($myvariation['attributes'])) {
            $terms=array();
            
            foreach ($myvariation['attributes'] as $key_a => $p_attr) {
              $data['variations'][$key_myv]['attributes'][$key_a]['id_product_idiotita']=0;
              $data['variations'][$key_myv]['attributes'][$key_a]['id_product_idiotita_term']=0;
              $p_attr_key=$p_attr['key'];
              $p_attr_value=$p_attr['value'];
              if (isset($data['attributes'][$p_attr_key])) {
                foreach ($data['attributes'][$p_attr_key]['terms'] as $a_index => $attr_term) {
                  if ($attr_term['slug']==$p_attr_value) {
                    $data['variations'][$key_myv]['attributes'][$key_a]['id_product_idiotita']=$data['attributes'][$p_attr_key]['id_product_idiotita'];
                    $data['variations'][$key_myv]['attributes'][$key_a]['id_product_idiotita_term']=$attr_term['id_product_idiotita_term'];
                    $data['variations'][$key_myv]['attributes'][$key_a]['name']=$attr_term['name'];
                    $terms[$attr_term['id_product_idiotita_term']]=0;
                    break;  
                  }
                }
              }
            }
  
            
            //print '<pre>';print_r($terms);die(); 
            
            
  
            
            //tropos 1: simfona me ta ids
            $sql="select * from gks_woo_product where eshop_id=".$eshop_id." and remote_product_id=".$myvariation['id'];
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            if ($result->num_rows>=1) {
              $row = $result->fetch_assoc(); 
              $var_id_product=$row['product_id'];
              $var_id_woo_product=$row['id_woo_product'];
              $var_remote_lang_db=trim_gks($row['remote_lang']);
              if ($remote_lang!='' and $remote_lang!=$var_remote_lang_db) {
                $sql="update gks_woo_product set remote_lang='".$db_link->escape_string($remote_lang)."' where id_woo_product=".$var_id_woo_product;
                $result = $db_link->query($sql);  
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
              }
                  
              
              $data['variations'][$key_myv]['id_product']=$row['product_id'];
              if (isset($exist_variations[$row['product_id']])) $exist_variations[$row['product_id']]['eshop_product_id']=$myvariation['id'];
            }
            
            if ($data['variations'][$key_myv]['id_product']==0) {// na psakso sta omoia me vasi ta attributes, prin ginei eisagogi
            //tropos 2: simfona me ta attributes na ginei i tautisi
              foreach ($exist_variations as $keyev => $myev) {
                if ($exist_variations[$keyev]['eshop_product_id']==0) {
                  $check_terms=$terms;   
                  foreach ($myev['product_idiotita_term_id'] as $term) {
                    if (isset($check_terms[$term])) $check_terms[$term]++;
                  }
                  $count_ok=0;
                  foreach ($check_terms as $term) {
                    if ($term==1) $count_ok++;
                  } 
                  if ($count_ok==count($check_terms) and $count_ok>0) {
                    $data['variations'][$key_myv]['id_product']=$myev['id_product'];
                    $exist_variations[$keyev]['eshop_product_id']=$myvariation['id'];
                    break;
                  }
                }
                //print '<pre>';print_r($check_terms);die(); 
              }
              
            }
            
            
            
            if ($data['variations'][$key_myv]['id_product']==0) { //add product
              $sql="insert into gks_eshop_products (
                mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                product_parent_id,product_class,product_parent_old_id
              ) values (
              '".$db_link->escape_string($myvariation['date_created'])."','".$db_link->escape_string($data['date_modified'])."',".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                ".$id_product.",'variable_item',0
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              $data['variations'][$key_myv]['id_product']=$db_link->insert_id;
              

              
              foreach ($terms as $term_key => $myterm) {
                $sql="insert into gks_eshop_products_variables (
                  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                  product_id,product_idiotita_term_id
                ) values (
                  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                  ".$data['variations'][$key_myv]['id_product'].",
                  ".$term_key."
                )";
                $result = $db_link->query($sql);  
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
              } 
            } else {
              $sql="update gks_eshop_products set 
              product_parent_id=".$id_product.",
              product_parent_old_id=0
              where id_product=".$data['variations'][$key_myv]['id_product'];
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              
              //fix attributes
              $ok_ids=array();
              foreach ($terms as $term_key => $myterm) {
                $sql="select id_product_variable from gks_eshop_products_variables 
                where product_id=".$data['variations'][$key_myv]['id_product']."
                and product_idiotita_term_id=".$term_key;
                $result = $db_link->query($sql);  
                if (!$result) {
                  debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                  return array('success' => false, 'message' => base64_encode('sql error'));}  
                
                if ($result->num_rows>0) {
                  $row = $result->fetch_assoc();
                  $ok_ids[]=$row['id_product_variable'];
                } else {
                  $sql="insert into gks_eshop_products_variables (
                    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                    product_id,product_idiotita_term_id
                  ) values (
                    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                    ".$data['variations'][$key_myv]['id_product'].",
                    ".$term_key."
                  )";
                  $result = $db_link->query($sql);  
                  if (!$result) {
                    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                    return array('success' => false, 'message' => base64_encode('sql error'));} 
                  $ok_ids[]=$db_link->insert_id;  
                }
              }
              
              $sql="delete from gks_eshop_products_variables 
              where product_id=".$data['variations'][$key_myv]['id_product'];
              if (count($ok_ids)>0) $sql.=" and id_product_variable not in (".implode(',',$ok_ids).")";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));} 
            }
            
            
            $sql="select id_woo_product from gks_woo_product 
            where eshop_id=".$eshop_id."
            and product_id=".$data['variations'][$key_myv]['id_product'];
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            if ($result->num_rows==0) {
              $sql="insert into gks_woo_product (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              eshop_id,product_id,remote_product_id,remote_lang
              ) values (
              now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
              ".$eshop_id.",".$data['variations'][$key_myv]['id_product'].",".$data['variations'][$key_myv]['id'].",
              '".$db_link->escape_string($remote_lang)."'
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
            } else {
              $sql="update gks_woo_product set 
              remote_product_id=".$data['variations'][$key_myv]['id'].",
              remote_lang='".$db_link->escape_string($remote_lang)."',
              mydate_edit=now(),
              user_id_edit=".$my_wp_user_id.",
              myip='".$db_link->escape_string($gkIP)."'
              where eshop_id=".$eshop_id."
              and product_id=".$data['variations'][$key_myv]['id_product']."
              and remote_product_id<>".$data['variations'][$key_myv]['id'];
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
            }
            
            
            
          }
          
          //product_descr_variable
          
          
          
          $sql="SELECT gks_product_idiotites_terms.idiotita_term_name
          FROM (gks_eshop_products_variables 
          LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_variables.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term) 
          LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita
          WHERE gks_eshop_products_variables.product_id=".$data['variations'][$key_myv]['id_product']."
          AND gks_product_idiotites_terms.idiotita_term_name<>''
          ORDER BY gks_product_idiotites.idiotita_sortorder, gks_product_idiotites_terms.idiotita_term_sortorder;";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
  
          $product_descr_variable_array=array();
          while ($row= $result->fetch_assoc()) { 
            $product_descr_variable_array[]=$row['idiotita_term_name'];
          }
          $product_descr_variable='';
          if (count($product_descr_variable_array)>0) $product_descr_variable=implode('-',$product_descr_variable_array);
          
          $product_photo='';
          if ($myvariation['image_id']>0) {
            foreach ($images as $index_image => $myimg) {
              if ($myimg['default']==false) {
                if ($myimg['id'] == $myvariation['image_id'] and isset($myimg['thumb_url'])) {
                  if ($myimg['local_ee_url']!='') {
                    $filename=mb_basename($myimg['local_ee_url']);
                    $product_photo = dirname($myimg['local_ee_url']).'/thumbnail/'.$filename;
                  } else { 
                    $product_photo=$myimg['thumb_url'];
                  }
                  break;
                }
              }
            }
          }
          
          $sql="select product_price,product_price_sale from gks_eshop_products where id_product=".$data['variations'][$key_myv]['id_product']." limit 1";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          $row= $result->fetch_assoc();
          $exist_product_price=floatval($row['product_price']);
          $exist_product_price_sale=floatval($row['product_price_sale']);
          
          $update_xondriki_price=false;
          if ($exist_product_price==0 and $exist_product_price_sale==0) {
            $update_xondriki_price=true;
            
            //product_price_retail_include_vat
          }
      
          
          $sql="update gks_eshop_products set 
          
          product_base_type=".$product_base_type.",
          product_can_sell=".$product_can_sell.",
          product_can_buy=".$product_can_buy.",
          
          product_photo='".$db_link->escape_string($product_photo)."',
          product_sku='".$db_link->escape_string($myvariation['sku'])."',
          product_gtin='".$db_link->escape_string($myvariation['gtin'])."',
          
          product_descr_variable='".$db_link->escape_string($product_descr_variable)."',
          product_descr_small='".$db_link->escape_string($myvariation['variation_description'])."',
          ";
  
          //product_descr='".$db_link->escape_string($data['name'])."',
          
          //product_descr_big='".$db_link->escape_string($data['description'])."',";
          
          if (intval($data['virtual'])==0) { // na einai opos to goniko
            $sql.="product_need_apostoli=1,";
          } else {
            $sql.="product_need_apostoli=0,";
          }
          $sql.="product_price_retail=".number_format(floatval($myvariation['regular_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
          $sql.="product_price_retail_sale=".number_format(floatval($myvariation['sale_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
          
          if (isset($myvariation['date_on_sale_from']) and trim_gks($myvariation['date_on_sale_from'])!='') 
            $sql.="product_price_retail_sale_from='".$db_link->escape_string($myvariation['date_on_sale_from'])."',";
          if (isset($myvariation['date_on_sale_to']) and trim_gks($myvariation['date_on_sale_to'])!='') 
            $sql.="product_price_retail_sale_to='".$db_link->escape_string($myvariation['date_on_sale_to'])."',";
          
          if ($update_xondriki_price) {
            $sql.="product_price=".number_format(floatval($myvariation['regular_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
            $sql.="product_price_sale=".number_format(floatval($myvariation['sale_price']), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",";
          }
          
          if ($woo_settings['weight_unit']=='kg') {
            $val=floatval($myvariation['weight'])*1000;
            $sql.="product_varos=".number_format($val,3,'.','').",";
          } else if ($woo_settings['weight_unit']=='g') {
            $val=floatval($myvariation['weight']);
            $sql.="product_varos=".number_format($val,3,'.','').",";
          } else if ($woo_settings['weight_unit']=='lbs') {
            $val=floatval($myvariation['weight'])*0.453592*1000;
            $sql.="product_varos=".number_format($val,3,'.','').",";
          } else if ($woo_settings['weight_unit']=='oz') {
            $val=floatval($myvariation['weight'])*0.0283495*1000;
            $sql.="product_varos=".number_format($val,3,'.','').",";
          } else {
            $val=floatval($myvariation['weight']);
            $sql.="product_varos=".number_format($val,3,'.','').",";
          }
        
          if ($woo_settings['dimension_unit']=='m') { //m cm mm in yd
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length'])*100,3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width'])*100,3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height'])*100,3,'.','').",";
          } else if ($woo_settings['dimension_unit']=='cm') { 
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length']),3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width']),3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height']),3,'.','').",";
          } else if ($woo_settings['dimension_unit']=='mm') { 
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length'])/10,3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width'])/10,3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height'])/10,3,'.','').",";
          } else if ($woo_settings['dimension_unit']=='in') { 
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length'])*2.54,3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width'])*2.54,3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height'])*2.54,3,'.','').",";
          } else if ($woo_settings['dimension_unit']=='yd') { 
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length'])*91.44,3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width'])*91.44,3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height'])*91.44,3,'.','').",";
          } else {
            $sql.="product_ogos_x=".number_format(floatval($myvariation['length']),3,'.','').",";
            $sql.="product_ogos_y=".number_format(floatval($myvariation['width']),3,'.','').",";
            $sql.="product_ogos_z=".number_format(floatval($myvariation['height']),3,'.','').",";
            
          }
          
          $product_fpa_base_id=0;  //	keno
          if ($woo_settings['calc_taxes']=='yes') {
            $sql.="product_price_retail_include_vat=".($woo_settings['prices_include_tax']=='yes' ? 1 : 0).",";
            if ($update_xondriki_price) $sql.="product_price_include_vat=".($woo_settings['prices_include_tax']=='yes' ? 1 : 0).",";
            if ($myvariation['tax_status']!='taxable') { //taxable none shipping
              $product_fpa_base_id=1004; //	No FPA
            } else {
              $product_fpa_base_id=1001;  //	Kanonikos
              if ($myvariation['tax_class']!='') {
                foreach ($woo_settings['wootaxes'] as $wtax) {
                  if ($myvariation['tax_class'] == $wtax['slug']) {
      //              echo $wtax['slug']."\n";
      //              echo $wtax['name']."\n";
      //              echo $eshop['tax_class_basikos']."\n";
      //              echo $eshop['tax_class_meiomenos']."\n";
      //              echo $eshop['tax_class_ypermeiomenos']."\n";
      //              echo $eshop['tax_class_xorisfpa']."\n";
                    
                    if ($eshop['tax_class_basikos']!='' and $wtax['name']==$eshop['tax_class_basikos']) $product_fpa_base_id=1001; //	Kanonikos
                    else if ($eshop['tax_class_meiomenos']!='' and $wtax['name']==$eshop['tax_class_meiomenos']) $product_fpa_base_id=1002; //	Meiomenos
                    else if ($eshop['tax_class_ypermeiomenos']!='' and $wtax['name']==$eshop['tax_class_ypermeiomenos']) $product_fpa_base_id=1003; //	Ypermeiomenos
                    else if ($eshop['tax_class_yperypermeiomenos']!='' and $wtax['name']==$eshop['tax_class_yperypermeiomenos']) $product_fpa_base_id=1005; //	Yper-Ypermeiomenos
                    else if ($eshop['tax_class_xorisfpa']!='' and $wtax['name']==$eshop['tax_class_xorisfpa'])  $product_fpa_base_id=1004; //	No FPA
                    break; 
                  } 
                }
              }  
            }
          } else {
            $sql.="product_price_retail_include_vat=1,";
            if ($update_xondriki_price) $sql.="product_price_include_vat=1,";
          }
          $sql.="product_fpa_base_id=".$product_fpa_base_id.",";
        
          $sql.="user_id_edit=".$my_wp_user_id.",
          myip='".$db_link->escape_string($gkIP)."'
          where id_product=".$data['variations'][$key_myv]['id_product']." limit 1";
          //mydate_edit='".$db_link->escape_string($data['date_modified'])."',
          
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
                  
          
          
        }
        
        //disable old variable_item
        $disable_ids=array();
        foreach ($exist_variations as $value) {
          if ($value['eshop_product_id']==0) {
            $disable_ids[]=$value['id_product'];
            
          }
        } 
        if (count($disable_ids)>0) {
          $sql="update gks_eshop_products set 
          product_parent_id=0,
          product_parent_old_id=".$id_product."
          where id_product in (".implode(',',$disable_ids).")";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
          
        }
        
      }
      
      if (1==1) {
        $sql="select * from gks_eshop_products_income where product_id=".$id_product;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        if ($result->num_rows==0) {
          $sql="insert into gks_eshop_products_income (
          product_id,aade_typos_xarakt_esodon_id,aade_katigoria_xarakt_esodon_id,acc_inv_product_income_pososto
          ) values (
          ".$id_product.",7,".($product_base_type==0 ? '1' : ($product_base_type==1 ? '2': '3')).",100
          )";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
        }
      }
       
      
      
      //echo '<pre>';print_r($data);print_r($exist_variations);die();
      
      
  //********************       categories start       *****************
      $product_category_id_array=array();
      if (isset($data['category_ids'])) {
        $woo_category_ids=array();
        foreach ($data['category_ids'] as $value) {
          $value=intval($value);
          if ($value>0) $woo_category_ids[]=$value;
        }
        
        if (count($woo_category_ids)>0) {
          $sql="select product_category_id from gks_woo_categories where eshop_id=".$eshop_id." and remote_category_id in (".implode(',',$woo_category_ids).")";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
          while ($row= $result->fetch_assoc()) {
            $product_category_id_array[]=$row['product_category_id'];
          }
        }
      }
      
      foreach ($product_category_id_array as $value) {
        $sql="select * from gks_eshop_products_categories_products where product_category_id=".$value." and product_id=".$id_product;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        if ($result->num_rows==0) {
          $sql="insert into gks_eshop_products_categories_products (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            product_category_id,product_id
          ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            ".$value.",".$id_product."
          )";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
        }
      }
      
      $sql="DELETE gks_eshop_products_categories_products.*
      FROM gks_eshop_products_categories_products 
      LEFT JOIN (
        SELECT gks_woo_categories.product_category_id
        FROM gks_woo_categories
        WHERE gks_woo_categories.eshop_id=".$eshop_id."
      )  AS bbbb ON gks_eshop_products_categories_products.product_category_id = bbbb.product_category_id
      WHERE bbbb.product_category_id Is Not Null
      and product_id=".$id_product;
      if (count($product_category_id_array)>0) 
        $sql.=" and gks_eshop_products_categories_products.product_category_id not in (".implode(',',$product_category_id_array).")";
  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
  //********************       categories end       *****************
  
  
  
  //********************       brands start       *****************
      $brands_plugins=array('berocket','woocommercebrand');
      
      foreach (GKS_ESHOP_BRANDS_TAXONOMY as $brand_as_idiotita) {
        $brands_plugins[]='gks-bai-'.$brand_as_idiotita['taxonomy'];
      }
      
      foreach ($brands_plugins as $pluginname) {
        if (isset($data['brands_plugins'][$pluginname]) and $data['brands_plugins'][$pluginname]['active']) {
          
          $product_brand_id_array=array();
          if (isset($data['brands_plugins'][$pluginname]['ids'])) {
            $woo_brands_ids=array();
            foreach ($data['brands_plugins'][$pluginname]['ids'] as $value) {
              $value=intval($value);
              if ($value>0) $woo_brands_ids[]=$value;
            }
            
            if (count($woo_brands_ids)>0) {
              $sql="select product_brand_id from gks_woo_brands 
              where eshop_id=".$eshop_id." 
              and pluginname='".$db_link->escape_string($pluginname)."'
              and remote_brand_id in (".implode(',',$woo_brands_ids).")";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              
              while ($row= $result->fetch_assoc()) {
                $product_brand_id_array[]=$row['product_brand_id'];
              }
            }
          }
          
          foreach ($product_brand_id_array as $value) {
            $sql="select * from gks_eshop_products_brands_products where product_brand_id=".$value." and product_id=".$id_product;
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            if ($result->num_rows==0) {
              $sql="insert into gks_eshop_products_brands_products (
                mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                product_brand_id,product_id
              ) values (
                now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                ".$value.",".$id_product."
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
            }
          }
          
          $sql="DELETE gks_eshop_products_brands_products.*
          FROM gks_eshop_products_brands_products 
          LEFT JOIN (
            SELECT gks_woo_brands.product_brand_id
            FROM gks_woo_brands
            WHERE gks_woo_brands.eshop_id=".$eshop_id."
            and remote_brand_id>0
            and pluginname='".$db_link->escape_string($pluginname)."'
          )  AS bbbb ON gks_eshop_products_brands_products.product_brand_id = bbbb.product_brand_id
          WHERE bbbb.product_brand_id Is Not Null
          and product_id=".$id_product;
          if (count($product_brand_id_array)>0) 
            $sql.=" and gks_eshop_products_brands_products.product_brand_id not in (".implode(',',$product_brand_id_array).")";
      
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
        }
      }
      
  //********************       brands end       *****************
      
      $sql="update gks_woo_product set 
      last_update_date=now(),
      last_update_user_id=".$my_wp_user_id."
      where id_woo_product=".$id_woo_product; //product_id=".$id_product." and eshop_id=".$eshop_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
  
  
  
  
      //echo '<pre>';print_r($product_category_id_array);die();
      //echo '<pre>';print_r($data);die();

    } else { //$remote_product_is_in_def_lang = false, diladi alli glosa
      $db_lang=gks_wpml_lang_convert_from_site_to_db($remote_lang);
      if ($db_lang!='') {
        $sql="select id_product_lang from gks_eshop_products_lang
        where product_id=".$id_product."
        and lang_code='".$db_link->escape_string($db_lang)."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
            
        if ($result->num_rows==0) {
          $sql="insert into gks_eshop_products_lang (
           mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
           product_id,lang_code,
           product_descr,product_descr_variable,product_descr_small,product_descr_big
          ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          ".$id_product.",
          '".$db_link->escape_string($db_lang)."',
          '".$db_link->escape_string($data['name'])."',
          '',
          '".$db_link->escape_string($data['short_description'])."',
          '".$db_link->escape_string($data['description'])."'
          )";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          
        } else {
          $row = $result->fetch_assoc();
          $id_product_lang=$row['id_product_lang'];

          $sql="update gks_eshop_products_lang set 
          product_descr='".$db_link->escape_string($data['name'])."',
          product_descr_small='".$db_link->escape_string($data['short_description'])."',
          product_descr_big='".$db_link->escape_string($data['description'])."',
          mydate_edit=now(),
          user_id_edit=".$my_wp_user_id.",
          myip='".$db_link->escape_string($gkIP)."'
          where id_product_lang=".$id_product_lang." limit 1";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
        }
        
        $sql="update gks_woo_product set 
        last_update_date=now(),
        last_update_user_id=".$my_wp_user_id."
        where product_id=".$id_product." and eshop_id=".$eshop_id." and remote_lang='".$remote_lang."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        
        //echo 'hhhhhhhhhhhhh';die();
        if (isset($data['variations']) and is_array($data['variations']) and count($data['variations'])>0) {
          foreach ($data['variations'] as $key_myv => $myvariation) {
            
            $var_remote_lang='';if (isset($myvariation['wpml_post_lang']['language_code'])) $var_remote_lang=$myvariation['wpml_post_lang']['language_code'];
            if (isset($myvariation['wpml_other_lang_ids']) and is_array($myvariation['wpml_other_lang_ids']) and count($myvariation['wpml_other_lang_ids'])) {
              
              foreach ($myvariation['wpml_other_lang_ids'] as $lang_item) {
                if ($eshop['wpml_default_lang_code']==$lang_item['lang']) { //vrisko tin default lang p.x. el
                  
                  if ($lang_item['lang']!='' and $lang_item['lang']!=$remote_lang && $lang_item['product_id']!=$myvariation['id']) {
                    $var_id_product=0;
                    //vrisko to antixo id gia tin elliniki parallagi
                    $sql="select * from gks_woo_product where remote_product_id=".$lang_item['product_id']." and eshop_id=".$eshop_id;
                    $result = $db_link->query($sql);  
                    if (!$result) {
                      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                      return array('success' => false, 'message' => base64_encode('sql error'));}                  
                    if ($result->num_rows>=1) {
                      $row = $result->fetch_assoc();
                      $var_id_product=$row['product_id'];
                      
                      //kano tin kataxorisi ston gks_woo_product gia aitin tin paralagi stin alli gloassa
                      $sql="select * from gks_woo_product where remote_product_id=".$myvariation['id']." and eshop_id=".$eshop_id;
                      $result = $db_link->query($sql);  
                      if (!$result) {
                        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                        return array('success' => false, 'message' => base64_encode('sql error'));}
                      if ($result->num_rows>=1) {
                        $row = $result->fetch_assoc();
                        if (intval($row['product_id'])!=$var_id_product or trim_gks($row['remote_lang'])!=$var_remote_lang) {
                          $sql="update gks_woo_product set
                          product_id=".$var_id_product.",
                          remote_lang='".$db_link->escape_string($var_remote_lang)."'
                          where id_woo_product=".$row['id_woo_product'];
                          $result = $db_link->query($sql);  
                          if (!$result) {
                            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                            return array('success' => false, 'message' => base64_encode('sql error'));} 
                        }
                      } else {
                        $sql="insert into gks_woo_product (
                          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                          product_id,eshop_id,remote_product_id,
                          remote_lang
                        ) values (
                          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                          ".$var_id_product.",".$eshop_id.",".$myvariation['id'].",
                          '".$db_link->escape_string($var_remote_lang)."'
                        )";
                        $result = $db_link->query($sql);  
                        if (!$result) {
                          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                          return array('success' => false, 'message' => base64_encode('sql error'));} 
                        //echo 'iiii ';
                      }
                      
                      $product_descr_variable_array=array();
                      if (isset($myvariation['attributes'])) {
                        foreach ($myvariation['attributes'] as $var_attr) {
                          $product_descr_variable_array[]=$var_attr['value'];
                        }   
                        
                      }
                      
                      
                      if (count($product_descr_variable_array)==0) {
                        $sql="SELECT gks_product_idiotites_terms.idiotita_term_name
                        FROM (gks_eshop_products_variables 
                        LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_variables.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term) 
                        LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita
                        WHERE gks_eshop_products_variables.product_id=".$var_id_product."
                        AND gks_product_idiotites_terms.idiotita_term_name<>''
                        ORDER BY gks_product_idiotites.idiotita_sortorder, gks_product_idiotites_terms.idiotita_term_sortorder;";
                        //echo '<pre>';print_r($myvariation);die();
                        //echo '<pre>'.$sql;die();
                        $result = $db_link->query($sql);  
                        if (!$result) {
                          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                          return array('success' => false, 'message' => base64_encode('sql error'));}  
                        
                        while ($row= $result->fetch_assoc()) { 
                          $product_descr_variable_array[]=$row['idiotita_term_name'];
                        }
                      }
                      $product_descr_variable='';
                      if (count($product_descr_variable_array)>0) $product_descr_variable=implode('-',$product_descr_variable_array);
                      
                      
                      
                      
                      
                      
                      $db_lang=gks_wpml_lang_convert_from_site_to_db($var_remote_lang);
                      if ($db_lang!='') {
                        $sql="select id_product_lang from gks_eshop_products_lang
                        where product_id=".$var_id_product."
                        and lang_code='".$db_link->escape_string($db_lang)."'";
                        $result = $db_link->query($sql);  
                        if (!$result) {
                          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                          return array('success' => false, 'message' => base64_encode('sql error'));}  
                            
                        if ($result->num_rows==0) {
                          $sql="insert into gks_eshop_products_lang (
                           mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                           product_id,lang_code,
                           product_descr,product_descr_variable,product_descr_small,product_descr_big
                          ) values (
                          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                          ".$var_id_product.",
                          '".$db_link->escape_string($db_lang)."',
                          '".$db_link->escape_string($myvariation['name'])."',
                          '".$db_link->escape_string($product_descr_variable)."',
                          '".$db_link->escape_string($myvariation['variation_description'])."',
                          ''
                          )";
                          $result = $db_link->query($sql);  
                          if (!$result) {
                            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                            return array('success' => false, 'message' => base64_encode('sql error'));}  
                          
                        } else {
                          $row = $result->fetch_assoc();
                          $id_product_lang=$row['id_product_lang'];
                
                          $sql="update gks_eshop_products_lang set 
                          product_descr='".$db_link->escape_string($myvariation['name'])."',
                          product_descr_variable='".$db_link->escape_string($product_descr_variable)."',
                          product_descr_small='".$db_link->escape_string($myvariation['variation_description'])."',
                          product_descr_big='',
                          mydate_edit=now(),
                          user_id_edit=".$my_wp_user_id.",
                          myip='".$db_link->escape_string($gkIP)."'
                          where id_product_lang=".$id_product_lang." limit 1";
                          $result = $db_link->query($sql);  
                          if (!$result) {
                            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                            return array('success' => false, 'message' => base64_encode('sql error'));}  
                        }
                        
                        //echo '<pre>ssss|'.$var_remote_lang.'|'.$db_lang.'|'.$var_id_product; print_r($data['variations'][0]);die();
                        
                      }
                      
                      
                      
                      //$var_id_product
                      
                    }
                    
                    
                  }
                }
              } 
            }
          } 
        }
        
        //echo '<pre>';print_r($data);die();
      }
    
    }
   
    $ret111111111=gks_products_update_barcodes(array($id_product));
    //ean einai error, min kaneis kati
     
  }
  //echo '<pre>ddddddd';die();
  
  
  if ($update_GKS_IDIOTITES_CACHE_VER) {
    $GKS_IDIOTITES_CACHE_VER=time();
    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
  }
  
  $save_but_message='';
  if ($will_update) {
    $save_but_message=gks_lang('Το είδος ενημερώθηκε από το WooCommerce');
  } else {
    $save_but_message=gks_lang('Το είδος δεν ενημερώθηκε από το WooCommerce διότι η τοπική εγγραφή είναι νεότερη από το WooCommerce');
  }
  //echo '<pre>will_update:'.$will_update;die();
  //print_r($images);
  
  //echo "\n\n\n\n";
  //print_r($eshop);
  //print_r($local);
  //print_r($data);
  //print_r($woo_settings);
  return array('success' => true, 'message' => base64_encode('OK'),'save_but_message'=>base64_encode($save_but_message));
}

 
function gks_woo_product_categories_update_local_from_woo($eshop, $categories_data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;

  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';

  $eshop_id=$eshop['id_eshop'];


  foreach ($categories_data as &$mycat) {
    
    //print '<pre>';print_r($mycat);die();
    
    $remote_category_id=intval($mycat['id']);
    $sql="select product_category_id from gks_woo_categories where remote_category_id=".$remote_category_id." and eshop_id=".$eshop_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $id_product_category=$row['product_category_id'];
      
      $sql="update gks_eshop_products_categories set
      product_category_descr='".$db_link->escape_string($mycat['descr'])."',
      category_comments='".$db_link->escape_string($mycat['description'])."',
      category_photo='".$db_link->escape_string($mycat['image'])."',
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_product_category=".$id_product_category;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}       

    } else {
      $sql="insert into gks_eshop_products_categories (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_category_parent_id,category_disable,
        product_category_descr,category_comments,category_photo
      ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        0,0,
        '".$db_link->escape_string($mycat['descr'])."',
        '".$db_link->escape_string($mycat['description'])."',
        '".$db_link->escape_string($mycat['image'])."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));} 
      
      $id_product_category = $db_link->insert_id;  
      
      $sql="insert into gks_woo_categories (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_category_id,eshop_id,remote_category_id
      ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$id_product_category.",".$eshop_id.",".$remote_category_id."
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}    
            
    }
    
    $mycat['id_product_category']=$id_product_category;
    $mycat['product_category_parent_id']=0;
    //$mycat['has_check_parent']=false;
  }
  unset($mycat);
  
  $sql="select product_category_id,remote_category_id from gks_woo_categories where eshop_id=".$eshop_id." and product_category_id>0 and remote_category_id>0";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}    
  $pantrema=array();
  while ($row= $result->fetch_assoc()) { 
    $pantrema[$row['remote_category_id']]=$row['product_category_id'];
  }
  //print '<pre>';print_r($pantrema);die();
  
  
  foreach ($categories_data as &$mycat) {
    if (isset($mycat['parent_id']) and $mycat['parent_id']>0) {
      if (isset($pantrema[$mycat['parent_id']])) {
        $mycat['product_category_parent_id']=$pantrema[$mycat['parent_id']];
      }
    }
  }
  unset($mycat);
  
  foreach ($categories_data as $mycat) {
    $sql="update gks_eshop_products_categories set
    product_category_parent_id=".$mycat['product_category_parent_id']."
    where id_product_category=".$mycat['id_product_category'];
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}    
  }
  
  $only_ids=array(); foreach ($categories_data as $mycat) $only_ids[]=$mycat['id_product_category'];
  if (count($only_ids)>0) {
    $sql="update gks_woo_categories set 
    last_update_user_id=".$my_wp_user_id.",
    last_update_date=now()
    where eshop_id=".$eshop_id." and product_category_id in (".implode(',',$only_ids).")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}    
  }
  

  //print '<pre>';print_r($categories_data);die();
  return array('success' => true, 'message' => base64_encode('OK'));
}


function gks_woo_product_brand_update_local_from_woo($eshop, $brand_data, $pluginname) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;

  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';

  $eshop_id=$eshop['id_eshop'];


  foreach ($brand_data as &$mybrand) {
    
    //print '<pre>';print_r($mybrand);die();
    
    $remote_brand_id=intval($mybrand['id']);
    $sql="select product_brand_id from gks_woo_brands 
    where remote_brand_id=".$remote_brand_id." 
    and pluginname='".$db_link->escape_string($pluginname)."'
    and eshop_id=".$eshop_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $id_product_brand=$row['product_brand_id'];
      
      $sql="update gks_eshop_products_brands set
      product_brand_descr='".$db_link->escape_string($mybrand['descr'])."',
      brand_comments='".$db_link->escape_string($mybrand['description'])."',
      brand_photo='".$db_link->escape_string($mybrand['image'])."',
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_product_brand=".$id_product_brand;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}       

    } else {
      $sql="insert into gks_eshop_products_brands (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_brand_parent_id,brand_disable,
        product_brand_descr,brand_comments,brand_photo
      ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        0,0,
        '".$db_link->escape_string($mybrand['descr'])."',
        '".$db_link->escape_string($mybrand['description'])."',
        '".$db_link->escape_string($mybrand['image'])."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));} 
      
      $id_product_brand = $db_link->insert_id;  
      
      $sql="insert into gks_woo_brands (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        product_brand_id,eshop_id,pluginname,remote_brand_id
      ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$id_product_brand.",".$eshop_id.",'".$db_link->escape_string($pluginname)."',".$remote_brand_id."
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}    
            
    }
    
    $mybrand['id_product_brand']=$id_product_brand;
    $mybrand['product_brand_parent_id']=0;
    //$mybrand['has_check_parent']=false;
  }
  unset($mybrand);
  
  $sql="select product_brand_id,remote_brand_id from gks_woo_brands 
  where eshop_id=".$eshop_id."
  and product_brand_id>0 
  and pluginname='".$db_link->escape_string($pluginname)."'
  and remote_brand_id>0";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}    
  $pantrema=array();
  while ($row= $result->fetch_assoc()) { 
    $pantrema[$row["remote_brand_id"]]=$row['product_brand_id'];
  }
  //print '<pre>';print_r($pantrema);die();
  
  
  foreach ($brand_data as &$mybrand) {
    if (isset($mybrand['parent_id']) and $mybrand['parent_id']>0) {
      if (isset($pantrema[$mybrand['parent_id']])) {
        $mybrand['product_brand_parent_id']=$pantrema[$mybrand['parent_id']];
      }
    }
  }
  unset($mybrand);
  
  foreach ($brand_data as $mybrand) {
    $sql="update gks_eshop_products_brands set
    product_brand_parent_id=".$mybrand['product_brand_parent_id']."
    where id_product_brand=".$mybrand['id_product_brand'];
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}    
  }
  
  $only_ids=array(); foreach ($brand_data as $mybrand) $only_ids[]=$mybrand['id_product_brand'];
  if (count($only_ids)>0) {
    $sql="update gks_woo_brands set 
    last_update_user_id=".$my_wp_user_id.",
    last_update_date=now()
    where eshop_id=".$eshop_id." 
    and pluginname='".$db_link->escape_string($pluginname)."'
    and product_brand_id in (".implode(',',$only_ids).") 
    and remote_brand_id>0";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}    
  }
  

  //print '<pre>';print_r($brand_data);die();
  return array('success' => true, 'message' => base64_encode('OK'));
}

function gks_woo_order_state_descr($s) {
  switch (trim_gks($s)) { 
    case 'pending':    return gks_lang('Εκκρεμεί πληρωμή','part4','gks_woo_order_state_descr');
    case 'processing': return gks_lang('Σε επεξεργασία','part4','gks_woo_order_state_descr');
    case 'on-hold':    return gks_lang('Σε αναμονή','part4','gks_woo_order_state_descr');
    case 'completed':  return gks_lang('Ολοκληρωμένη','part4','gks_woo_order_state_descr');
    case 'cancelled':  return gks_lang('Ακυρωμένη','part4','gks_woo_order_state_descr');
    case 'refunded':   return gks_lang('Επιστροφή χρημάτων','part4','gks_woo_order_state_descr');
    case 'failed':     return gks_lang('Αποτυχημένη','part4','gks_woo_order_state_descr');
    default:
      return $s;
  }
	return $s;  
}
function gks_woo_convert_state_to_transfer($s){
  switch (trim_gks($s)) { 
    case 'pending': 
      return '070wait_payment';
    case 'processing': 
      return '080confirm';
    case 'on-hold': 
      return '070wait_payment';
    case 'completed': 
      return '080confirm';
    case 'cancelled': 
      return '040cancelled';
    case 'refunded': 
      return '050rejected';
    case 'failed': 
      return '050rejected';
    default:  
      return '010draft';
  }
	return '010draft';
}


function gks_woo_convert_state_to_reservation($s){
  switch (trim_gks($s)) { 
    case 'pending': 
      return '070wait_payment';
    case 'processing': 
      return '080confirm';
    case 'on-hold': 
      return '070wait_payment';
    case 'completed': 
      return '080confirm';
    case 'cancelled': 
      return '040cancelled';
    case 'refunded': 
      return '050rejected';
    case 'failed': 
      return '050rejected';
    default:  
      return '010draft';
  }
	return '010draft';
}

function gks_woo_convert_state_to_order($s){
  switch (trim_gks($s)) { 
    case 'pending': 
      return '055wait_payment';
    case 'processing': 
      return '060registered';
    case 'on-hold': 
      return '020pending';
    case 'completed': 
      return '095execute';
    case 'cancelled': 
      return '040cancelled';
    case 'refunded': 
      return '050rejected';
    case 'failed': 
      return '080failed';
    default:  
      return '010draft';
  }
	return '010draft';
}

function gks_woo_convert_state_to_acc_inv($s){
  switch (trim_gks($s)) { 
    case 'pending': 
      return '010draft';
    case 'processing': 
      return '010draft';
    case 'on-hold': 
      return '010draft';
    case 'completed': 
      return '050proinvoice';
    case 'cancelled': 
      return '040cancelled';
    case 'refunded': 
      return '040cancelled';
    case 'failed':
      return '040cancelled';
    default:  
      return '010draft';
  }
	return '010draft';
}



