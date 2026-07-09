<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_woo_comments_order_update_local_from_woo($eshop,$data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_ORDER_DEFAULT_DELIVERY;
  global $GKS_ORDER_DEFAULT_PAYMENT;

  //print '<pre>';print_r($data);die();
  //return array('success' => false, 'message' => base64_encode('<pre>data start '.print_r($data, true)));


  $comments_list=$data['comments_list'];
  if (count($comments_list)==0) {
    return array('success' => true, 'message' => base64_encode('OK'), 'save_but_message'=>base64_encode('no records'));}
  
    
  
  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';
  
  
  $eshop_id=$eshop['id_eshop'];
  //print '<pre>';print_r($eshop);
  $woo_order_id=intval($data['id']);
  if ($woo_order_id<=0) {
    debug_mail(false,'woo_order_id is not set',print_r($data,true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο αριθμός παραγγελίας')));} 
  
  
  

  
  if ($eshop['import_as']=='transfer') { 
    $table_into         ='gks_transfer_reservation';
    $table_into_messages='gks_transfer_reservation_messages';
    $table_id  ='id_transfer_reservation';
    $table_idr ='transfer_reservation_id';
    $field_message ='transfer_reservation_message';
  } else if ($eshop['import_as']=='reservation') { 
    $table_into         ='gks_hotel_reservation';
    $table_into_messages='gks_hotel_reservation_messages';
    $table_id  ='id_hotel_reservation';
    $table_idr ='hotel_reservation_id';
    $field_message ='hotel_reservation_message';
  } else if ($eshop['import_as']=='order') {
    $table_into         ='gks_orders';
    $table_into_messages='gks_orders_messages';
    $table_id  ='id_order';
    $table_idr ='order_id';
    $field_message ='order_message';
  } else if ($eshop['import_as']=='acc_inv') {
    $table_into         ='gks_acc_inv';
    $table_into_messages='gks_acc_inv_messages';
    $table_id  ='id_acc_inv';
    $table_idr ='acc_inv_id';
    $field_message ='acc_inv_message';
  } else {
    return array('success' => false, 'message' => base64_encode('erro on import_as'));
  }

  $sql="select ".$table_id." as myrecid from ".$table_into." where woo_eshop_id=".$eshop_id." and woo_order_id=".$woo_order_id;
  if ($table_into=='gks_transfer_reservation') {
    $sql.=" and woo_guid not like '%.return'";
  }
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}  

  if ($result->num_rows==0) {
    debug_mail(false,'remote record not found sql',$sql);
    return array('success' => false, 'message' => base64_encode('remote record not found'));}  
  
  $kataxoreiseis_ids=array();  
  while ($row = $result->fetch_assoc()) {
    $kataxoreiseis_ids[]=$row['myrecid'];
  }
  
  foreach ($kataxoreiseis_ids as $kat_id) {
    $sql="select woo_comment_id from ".$table_into_messages." where ".$table_idr."=".$kat_id." and woo_eshop_id=".$eshop_id." and woo_comment_id>0";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['woo_comment_id'];
    }
    
    foreach ($comments_list as $myc) {
      if (in_array($myc['id'],$exist_ids)==false) {
        $user_id=2;
        $myip =trim_gks($myc['author_ip']);
        $woo_author=trim_gks($myc['author']);
        if ($woo_author=='WooCommerce') {
          $woo_author='';
          $user_id=2;
        }
        
        $meta_text=[];
        foreach ($myc['meta'] as $metaitem) {
          if ($metaitem['key']=='is_customer_note') {
            if ($metaitem['value']==1) $meta_text[]='<span class="customer_note">'.gks_lang('Σημείωση σε πελάτη').'</span>';
          } else {
            $meta_text[]='<span class="meta">'.trim_gks($metaitem['key']).': '.trim_gks($metaitem['value']).'</spam>';
          }
        }
        
        $content=trim_gks($myc['content']);
        if (count($meta_text)>0) {
          $content.='<br><span class="messages_meta_woo">'.implode('<br>',$meta_text).'</span>';
        }
        
        $sql="insert into ".$table_into_messages." (
          ".$table_idr.",
          mydate_add,mydate_edit,
          user_id_add,user_id_edit,
          myip,
          user_id,
          ".$field_message.",
          woo_eshop_id,woo_comment_id,
          woo_author
        ) values (
          ".$kat_id.",
          '".$db_link->escape_string($myc['date_gmt'])."','".$db_link->escape_string($myc['date_gmt'])."',
          ".$user_id.",".$user_id.",
          '".$db_link->escape_string($myip)."',
          ".$user_id.",
          '".$db_link->escape_string($content)."',
          ".$eshop_id.",
          ".intval($myc['id']).",
          '".$db_link->escape_string($woo_author)."'
          
        )";  
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
      }
    } 
  }

  $save_but_message='';
  return array('success' => true, 'message' => base64_encode('OK'),'save_but_message'=>base64_encode($save_but_message));

  
  $save_but_message='';
  return array('success' => false, 'message' => base64_encode('OK '.$table_into_log.' '.print_r($kataxoreiseis_ids,true).' '.print_r($comments_list,true)),'save_but_message'=>base64_encode($save_but_message));
  
}


