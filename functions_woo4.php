<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_woo_coupon_update_local_from_woo($eshop,$data,$woo_settings,$force) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_ORDER_DEFAULT_DELIVERY;
  global $GKS_ORDER_DEFAULT_PAYMENT;


  
  //print '<pre>';print_r($data);die();
  //return array('success' => false, 'message' => base64_encode('<pre>data start '.print_r($data, true)));
  
  //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/ggg.txt', print_r($data,true));
  
  if ($force==0 and (isset($eshop['import_yes']) ==false or trim_gks($eshop['import_yes'])=='' or $eshop['import_yes']==0)) {
    return array('success' => true, 'message' => base64_encode(gks_lang('Δεν είναι ενεργοποιημένο το').': <b>'.gks_lang('Να γίνεται αυτόματη εισαγωγή στο WooCommerce').'</b>'));}
  
  
    
  
  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';
  
  //print '<pre>';print_r($data);die();

  
  
  $eshop_id=$eshop['id_eshop'];
  //print '<pre>';print_r($eshop);
  
  foreach ($data as $citem) {

    $woo_coupon_id=intval($citem['id']);
    if ($woo_coupon_id<=0) {
      debug_mail(false,'woo_coupon_id is not set',print_r($data,true));
      return array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο αριθμός κουπονιού')));} 
    
    //print '<pre>';print_r($citem);die();
    
    $id_pricelist_item=0;
    $sql="SELECT gks_woo_coupons.pricelist_item_id
    FROM gks_woo_coupons 
    LEFT JOIN gks_eshop_pricelist_items ON gks_woo_coupons.pricelist_item_id = gks_eshop_pricelist_items.id_pricelist_item
    WHERE gks_woo_coupons.remote_coupon_id=".$woo_coupon_id."
    AND gks_woo_coupons.eshop_id=".$eshop_id."
    AND gks_eshop_pricelist_items.id_pricelist_item Is Not Null";
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
    if ($result->num_rows==0) {
      
      $sql="insert into gks_eshop_pricelist_items (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        pricelist_id
      ) values (
        '".$db_link->escape_string($citem['gks_date_created'])."','".$db_link->escape_string($citem['gks_date_modified'])."',
        2,2,'".$db_link->escape_string($gkIP)."',
        1
      )";

      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
     
      $id_pricelist_item=$db_link->insert_id; 
      
      //diadgafi skoupidion
      $sql="delete from gks_woo_coupons 
      where remote_coupon_id=".$woo_coupon_id."
      AND eshop_id=".$eshop_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      
      
      $sql="insert into gks_woo_coupons (
       mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
       pricelist_item_id,eshop_id,remote_coupon_id,last_update_user_id,last_update_date
      ) values (
        now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
        ".$id_pricelist_item.",
        ".$eshop_id.",
        ".$woo_coupon_id.",
        2,now()
      )";
      
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      
    } else {
      $row = $result->fetch_assoc();
      $id_pricelist_item=$row['pricelist_item_id'];      
      
    }
    
    //print '<pre>';print_r($citem);die();
    
    $amount=floatval($citem['amount']);
    $discount_type=trim_gks($citem['discount_type']);
    if ($discount_type=='percent') { //ekptosi epi tis %
      $pricelist_item_price_epi=-($amount/100);
      $pricelist_item_price_plus=0;
    } else if ($discount_type=='fixed_cart') { //statheri ekptosi sto kalathi
      $pricelist_item_price_epi=0;
      $pricelist_item_price_plus=-$amount;
    } else { //fixed_product statheri ekptosis proiontos
      $pricelist_item_price_epi=0;
      $pricelist_item_price_plus=-$amount;
    }
    
    
    $pricelist_item_date_to='null'; if (!empty($citem['gks_date_expires'])) $pricelist_item_date_to="'".$citem['gks_date_expires']."'";
     
    $email_restrictions='';
    if (is_string($citem['email_restrictions'])) $email_restrictions=$citem['email_restrictions'];
    else if (is_array($citem['email_restrictions'])) $email_restrictions=implode(', ',$citem['email_restrictions']); 
    
    $sql="update gks_eshop_pricelist_items set 
    pricelist_item_coupon='".$db_link->escape_string(trim_gks($citem['code']))."',
    pricelist_item_descr='".$db_link->escape_string(trim_gks($citem['description']))."',
    pricelist_item_disable=".(trim_gks($citem['status'])=='publish' ? '0' : '1').",
    pricelist_item_date_to=".$pricelist_item_date_to.",
    pricelist_item_price_epi=".$pricelist_item_price_epi.",
    pricelist_item_price_plus=".$pricelist_item_price_plus.",
    pricelist_item_price_eval=null,
    pricelist_item_individual_use=".intval($citem['individual_use']).",
    pricelist_item_usage_limit=".intval($citem['usage_limit']).",
    pricelist_item_usage_limit_per_user=".intval($citem['usage_limit_per_user']).",
    pricelist_item_limit_usage_to_x_items=".intval($citem['limit_usage_to_x_items']).",
    pricelist_item_min_price=".floatval($citem['minimum_amount']).",
    pricelist_item_max_price=".floatval($citem['maximum_amount']).",
    pricelist_item_exclude_sale_items=".intval($citem['exclude_sale_items']).",
    pricelist_item_users_emails=".intval($citem['exclude_sale_items']).",
    pricelist_item_users_emails='".$db_link->escape_string($email_restrictions)."',
    
    mydate_edit=now(),
    user_id_edit=2,
    myip='".$db_link->escape_string($gkIP)."'
    where id_pricelist_item=".$id_pricelist_item;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  


    //products start
    {
      $not_delete_ids=[];
      $sql="select id_pricelist_item_product,product_id,is_include
      from gks_eshop_pricelist_items_products
      where pricelist_item_id=".$id_pricelist_item;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      $exist_records=[];
      while ($row = $result->fetch_assoc()) { 
        $exist_records[]=$row;
      }
      if (isset($citem['product_ids']) and is_array($citem['product_ids']) and count($citem['product_ids'])>0) {
        $sql="select product_id from gks_woo_product 
        where eshop_id=".$eshop_id." and remote_product_id in (".implode(',',$citem['product_ids']).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $list=[];
        while ($row = $result->fetch_assoc()) { 
          $list[]=$row['product_id'];
        }
        
        foreach ($list as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_id']==$vitem and $erec['is_include']==1) {
              $not_delete_ids[]=$erec['id_pricelist_item_product'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_products (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      if (isset($citem['excluded_product_ids']) and is_array($citem['excluded_product_ids']) and count($citem['excluded_product_ids'])>0) {
        $sql="select product_id from gks_woo_product 
        where eshop_id=".$eshop_id." and remote_product_id in (".implode(',',$citem['excluded_product_ids']).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $list=[];
        while ($row = $result->fetch_assoc()) { 
          $list[]=$row['product_id'];
        }
        
        foreach ($list as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_id']==$vitem and $erec['is_include']==-1) {
              $not_delete_ids[]=$erec['id_pricelist_item_product'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_products (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",-1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      $sql="delete from gks_eshop_pricelist_items_products where pricelist_item_id=".$id_pricelist_item;
      if (count($not_delete_ids)>0) {
        $sql.=" and id_pricelist_item_product not in (".implode(',',$not_delete_ids).")";
      }
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      //products end
    }
    
    //categories start
    {
      $not_delete_ids=[];
      $sql="select id_pricelist_item_category,product_category_id,is_include
      from gks_eshop_pricelist_items_categories
      where pricelist_item_id=".$id_pricelist_item;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      $exist_records=[];
      while ($row = $result->fetch_assoc()) { 
        $exist_records[]=$row;
      }
      if (isset($citem['product_categories']) and is_array($citem['product_categories']) and count($citem['product_categories'])>0) {
        $sql="select product_category_id from gks_woo_categories 
        where eshop_id=".$eshop_id." and remote_category_id in (".implode(',',$citem['product_categories']).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $list=[];
        while ($row = $result->fetch_assoc()) { 
          $list[]=$row['product_category_id'];
        }
        
        foreach ($list as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_category_id']==$vitem and $erec['is_include']==1) {
              $not_delete_ids[]=$erec['id_pricelist_item_category'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_categories (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_category_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      if (isset($citem['excluded_product_categories']) and is_array($citem['excluded_product_categories']) and count($citem['excluded_product_categories'])>0) {
        $sql="select product_category_id from gks_woo_categories 
        where eshop_id=".$eshop_id." and remote_category_id in (".implode(',',$citem['excluded_product_categories']).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        $list=[];
        while ($row = $result->fetch_assoc()) { 
          $list[]=$row['product_category_id'];
        }
        
        foreach ($list as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_category_id']==$vitem and $erec['is_include']==-1) {
              $not_delete_ids[]=$erec['id_pricelist_item_category'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_categories (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_category_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",-1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      $sql="delete from gks_eshop_pricelist_items_categories where pricelist_item_id=".$id_pricelist_item;
      if (count($not_delete_ids)>0) {
        $sql.=" and id_pricelist_item_category not in (".implode(',',$not_delete_ids).")";
      }
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      //categories end
    }
    
    //brands start
    {
      $not_delete_ids=[];
      $sql="select id_pricelist_item_brand,product_brand_id,is_include
      from gks_eshop_pricelist_items_brands
      where pricelist_item_id=".$id_pricelist_item;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      $exist_records=[];
      while ($row = $result->fetch_assoc()) { 
        $exist_records[]=$row;
      }
      
      $brands_include=[];
      $brands_exclude=[];
      if (isset($citem['gks_meta_data']) and is_array($citem['gks_meta_data'])) {
        $ber_in=[];$ber_ex=[];$woo_in=[];$woo_ex=[];
        foreach ($citem['gks_meta_data'] as $mdv) {
          if ($mdv['key']=='berocket_brand')          foreach ($mdv['value'] as $vv) $ber_in[]=$vv;
          if ($mdv['key']=='exclude_berocket_brand')  foreach ($mdv['value'] as $vv) $ber_ex[]=$vv;
          if ($mdv['key']=='product_brands')          foreach ($mdv['value'] as $vv) $woo_in[]=$vv;
          if ($mdv['key']=='exclude_product_brands')  foreach ($mdv['value'] as $vv) $woo_ex[]=$vv;
        }
        
        if (count($ber_in)>0) {
          $sql="select product_brand_id from gks_woo_brands 
          where eshop_id=".$eshop_id." and remote_brand_id in (".implode(',',$ber_in).") and pluginname='berocket'";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          while ($row = $result->fetch_assoc()) $brands_include[]=$row['product_brand_id'];
        }
        if (count($ber_ex)>0) {
          $sql="select product_brand_id from gks_woo_brands 
          where eshop_id=".$eshop_id." and remote_brand_id in (".implode(',',$ber_ex).") and pluginname='berocket'";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          while ($row = $result->fetch_assoc()) $brands_exclude[]=$row['product_brand_id'];
        }

        if (count($woo_in)>0) {
          $sql="select product_brand_id from gks_woo_brands 
          where eshop_id=".$eshop_id." and remote_brand_id in (".implode(',',$woo_in).") and pluginname='woocommercebrand'";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          while ($row = $result->fetch_assoc()) $brands_include[]=$row['product_brand_id'];
        }
        if (count($woo_ex)>0) {
          $sql="select product_brand_id from gks_woo_brands 
          where eshop_id=".$eshop_id." and remote_brand_id in (".implode(',',$woo_ex).") and pluginname='woocommercebrand'";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
            return array('success' => false, 'message' => base64_encode('sql error'));}  
          while ($row = $result->fetch_assoc()) $brands_exclude[]=$row['product_brand_id'];
        }        
      }
      
      //print '<pre>';print_r($brands_include);print_r($brands_exclude);die();
      
      
      if (count($brands_include)>0) {
        foreach ($brands_include as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_brand_id']==$vitem and $erec['is_include']==1) {
              $not_delete_ids[]=$erec['id_pricelist_item_brand'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_brands (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_brand_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      if (count($brands_exclude)>0) {
        foreach ($brands_exclude as $vitem) {
          $found=false;
          foreach ($exist_records as $erec) {
            if ($erec['product_brand_id']==$vitem and $erec['is_include']==-1) {
              $not_delete_ids[]=$erec['id_pricelist_item_brand'];
              $found=true;
              break;  
            }
          } 
          if ($found==false) {
            $sql="insert into gks_eshop_pricelist_items_brands (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              pricelist_item_id,product_brand_id,is_include
            ) values (
              now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
              ".$id_pricelist_item.",".$vitem.",-1
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
            $not_delete_ids[]=$db_link->insert_id;  
          }
        }
      }
      $sql="delete from gks_eshop_pricelist_items_brands where pricelist_item_id=".$id_pricelist_item;
      if (count($not_delete_ids)>0) {
        $sql.=" and id_pricelist_item_brand not in (".implode(',',$not_delete_ids).")";
      }
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      //brands end
    }
        
    //echo '<pre>';print_r($list);print_r($not_delete_ids);print_r($exist_records);die();
    



    //woo_import_coupon_after
    gks_plugins_functions_run('woo_import_coupon_after',array(
      'eshop'=>&$eshop,
      'woo_coupon_data'=>&$citem,
      'woo_settings'=>&$woo_settings,
      'force'=>&$force,
      'id_pricelist_item'=>&$id_pricelist_item
    ));
    

    $table_into='gks_eshop_pricelist_items';
    $remote_url=GKS_SITE_URL.'my/admin-pricelists-items-item.php?id='.$id_pricelist_item;
      
    $woo_back_meta_data[] = array('obj' => $table_into, 'id' => $id_pricelist_item, 'url' => $remote_url);
  
    $data_send = array(
    	'cmd'=>'set_coupon_id',
    	'id_coupon'=>$woo_coupon_id, 
      'woosettings' => false,
      'data'=>$woo_back_meta_data,
    );
    //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($data,true)));
    
    $ret=gks_woo_post($eshop, $data_send);
    
    
    
  } 
  

  return array('success' => true, 'message' => base64_encode('OK'),'save_but_message'=>base64_encode('OK xa !'));
  
}



