<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$term='';if (isset($_POST['term'])) $term=trim_gks(base64_decode($_POST['term']));


if (mb_strlen($term)<3) {
  $message='<div class="alert alert-danger" role="alert" style="text-align:center;">'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'</div>';
  $return = array('success' => true, 'message' => base64_encode($message));echo json_encode($return); die();
}

$term=str_replace('*','%',$term);
$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
} 

$my_page_title=gks_lang('Αναζήτηση για').': '.$term;
db_open();
stat_record();

$html='';

$term_digit=0;
if (ctype_digit($term)) {
  $term_digit=intval($term);
  if ($term_digit<0) $term_digit=0;
}


if ($GKS_HOTEL_BACKEND) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation','view',0);
  if ($perm_ret['success']) {
    $sql="select id_hotel_reservation as id,hotel_booking_number,gks_hotel.hotel_color 
    from gks_hotel_reservation
    LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel
    where ";
    if ($term_digit>0) $sql.="id_hotel_reservation=".$term_digit." or ";
    $sql.=" hotel_booking_number like '%".$db_link->escape_string($term)."%' limit 10";

    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {  
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-hotel-reservation-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-hotel-reservation-item.php?id='.$row['id'].'">'.gks_lang('Κράτηση Ξενοδοχείου').' <b>'.$row['id'].'</b> ';
          if (empty($row['hotel_booking_number'])==false) {
            $item.= '<span class="ref_number_table_td_span" style="background-color:'.$row['hotel_color'].';">'.$row['hotel_booking_number'].'</span>';
          }          
      $item.=               
          '</a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item;  
      }      
    }
  }      
}

if (GKS_TRANSFER) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_transfer_reservation','view',0);
  if ($perm_ret['success']) {
    $sql="select id_transfer_reservation as id,transfer_booking_number from gks_transfer_reservation where ";
    if ($term_digit>0) $sql.="id_transfer_reservation=".$term_digit." or ";
    $sql.=" transfer_booking_number like '%".$db_link->escape_string($term)."%' limit 10";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {  
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-transfer-reservation-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-transfer-reservation-item.php?id='.$row['id'].'">'.gks_lang('Κράτηση Transfer').'<b>'.$row['id'].'</b> ';
          
          if (empty($row['transfer_booking_number'])==false) {
            $item.= '<span class="ref_number_table_td_span">'.$row['transfer_booking_number'].'</span>';
          }
          
      $item.=               
          '</a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item;  
      }      
    }
  } 
}
    
if ($term_digit>0) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','view',0);
  if ($perm_ret['success']) {
    $sql="select id_whi_mov as id from gks_whi_mov where id_whi_mov=".$term_digit;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {  
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-whi-mov-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-whi-mov-item.php?id='.$row['id'].'">'.gks_lang('Δελτίο Αποστολής').' <b>'.$row['id'].'</b></a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item;  
      }      
    }
  }
}

//if ($term_digit>0) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','view',0);
  if ($perm_ret['success']) {
    
    
    $sql="select id_order as id,order_ref_number from gks_orders where ";
    if ($term_digit>0) $sql.="id_order=".$term_digit." or ";
    $sql.=" order_ref_number like '%".$db_link->escape_string($term)."%' limit 10";
    gks_plugins_functions_run('admin_header_search_exec_order_sql',array(
      'sql'=>&$sql,
      'term_digit'=>&$term_digit,
      'term'=>&$term,
    ));
              

    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {  
      
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-orders-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-orders-item.php?id='.$row['id'].'">'.gks_lang('Παραγγελία').' <b>'.$row['id'].'</b> ';
          
        gks_plugins_functions_run('admin_header_search_exec_order_item',array(
          'item'=>&$item,
          'row'=>&$row,
        ));
                    
        if (empty($row['order_ref_number'])==false) {
          $item.= '<span class="ref_number_table_td_span">'.$row['order_ref_number'].'</span>';
        }        
        $item.=  
          '</a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item;        
      }
    }
  }
//}

//if ($term_digit>0) {    
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','view',0);
  if ($perm_ret['success']) {
    $sql="select id_acc_inv as id,acc_inv_ref_number from gks_acc_inv where ";
    if ($term_digit>0) $sql.="id_acc_inv=".$term_digit." or ";
    $sql.=" acc_inv_ref_number like '%".$db_link->escape_string($term)."%' limit 10";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {  
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-acc-inv-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-acc-inv-item.php?id='.$row['id'].'">'.gks_lang('Παραστατικό').' <b>'.$row['id'].'</b> ';
        if (empty($row['acc_inv_ref_number'])==false) {
          $item.= '<span class="ref_number_table_td_span">'.$row['acc_inv_ref_number'].'</span>';
        }           
        $item.=  
          '</a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item; 
      }       
    }
  }
//}

if ($term_digit>0) {    
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','view',0);
  if ($perm_ret['success']) {
    $sql="select id_acc_pay as id from gks_acc_pay where id_acc_pay=".$term_digit;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();}
    
    if ($result->num_rows>=1) {
      while ($row = $result->fetch_assoc()) {
        $item=
        '<div class="gks_header_search_results_item" style="padding: 5px 0px;">'.
          '<div class="gks_header_search_results_right">'.
            '<a href="admin-acc-pay-item.php?id='.$row['id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
          '</div>'.
          '<a href="admin-acc-pay-item.php?id='.$row['id'].'">'.gks_lang('Πληρωμή').' <b>'.$row['id'].'</b></a>'.
          '<div style="clear: both;"></div>'.
        '</div>';
        $html.=$item; 
      }       
    }
  }
}


$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']) {
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities,gks_wsl_current_user_image
  FROM ".GKS_WP_TABLE_PREFIX."users
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id 
  where  ";
  if ($term_digit>0) $sql.=GKS_WP_TABLE_PREFIX."users.ID = ".$term_digit." or (";
  $sql.=' (';
  $mywhere='';
  foreach ($term_array as $value) {
    $value_en = greekkeybord($value);
    $mywhere.=" (
  
    ".GKS_WP_TABLE_PREFIX."users.user_login like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_nicename like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_email like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_url like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_fullname like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_mobile like '%".$db_link->escape_string($value)."%' or
    ".GKS_WP_TABLE_PREFIX."users.comm_search like '%".$db_link->escape_string($value)."%' or
    gks_users.phone_home like '%".$db_link->escape_string($value)."%' or
    gks_users.eponimia like '%".$db_link->escape_string($value)."%' or
    gks_users.title like '%".$db_link->escape_string($value)."%' or
    gks_users.afm like '%".$db_link->escape_string($value)."%' or
    gks_users.doy like '%".$db_link->escape_string($value)."%' or
    gks_users.epaggelma like '%".$db_link->escape_string($value)."%' or
    gks_users.ma_poli like '%".$db_link->escape_string($value)."%' or

    ".GKS_WP_TABLE_PREFIX."users.user_login like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_nicename like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_email like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.user_url like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_fullname like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname like '%".$db_link->escape_string($value_en)."%' or 
    ".GKS_WP_TABLE_PREFIX."users.gks_mobile like '%".$db_link->escape_string($value_en)."%' or
    ".GKS_WP_TABLE_PREFIX."users.comm_search like '%".$db_link->escape_string($value_en)."%' or
    gks_users.phone_home like '%".$db_link->escape_string($value_en)."%' or
    gks_users.eponimia like '%".$db_link->escape_string($value_en)."%' or
    gks_users.title like '%".$db_link->escape_string($value_en)."%' or
    gks_users.afm like '%".$db_link->escape_string($value_en)."%' or
    gks_users.doy like '%".$db_link->escape_string($value_en)."%' or
    gks_users.epaggelma like '%".$db_link->escape_string($value_en)."%' or
    gks_users.ma_poli like '%".$db_link->escape_string($value_en)."%'     
    
    ) and ";
  }
    
    
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")";
  
  if ($term_digit>0) $sql.=")";
  $sql.=" order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname limit 10";  
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('error sql'));
    echo json_encode($return); die();}
  $html_epafes='';    
  while ($row = $result->fetch_assoc()) {
    
    $myimg=trim_gks($row['gks_wsl_current_user_image']);
    if ($myimg != '') {
      $myimg='<div class="gks_header_search_results_img"><a href="/my/admin-users-item.php?id='.$row['ID'].'">'.
      '<img src="'.$myimg.'" border="0" style="max-width:32px;max-height:32px;"/></a></div>';
    }

    $item=
    '<div class="gks_header_search_results_item">'.
      $myimg.
      '<div class="gks_header_search_results_right">'.
        (($term_digit > 0 and $term_digit==$row['ID']) ? '<b>'.$row['ID'].'</b>' : $row['ID']).
        '<br>'.
        '<a href="admin-users-item.php?id='.$row['ID'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
        '<a href="admin-users-item-card.php?id='.$row['ID'].'"><i class="fas fa-list-alt gks_user_item_card" title="'.gks_lang('Οικονομική Καρτέλα').'"></i></a>'.
        '<a href="admin-users-item-overview.php?id='.$row['ID'].'"><i class="fas fa-list-alt gks_user_item_overview" title="'.gks_lang('Επισκόπηση').'"></i></a>'.
      '</div>'.
      
      '<a href="admin-users-item.php?id='.$row['ID'].'">'.$row['gks_nickname'].'</a>'.
      '<div style="clear: both;"></div>'.
    '</div>';
    $html_epafes.=$item;
  }
  if ($html_epafes!='') {
      $html.= '<div class="gks_header_search_results_s">'.gks_lang('Επαφές').'</div>'.
              $html_epafes;
  }
}


$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','autocomplete',0);
if ($perm_ret['success']) {
  
  $sql="SELECT 
  gks_eshop_products.id_product,
  gks_eshop_products.product_code,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo
  
  END as product_photo_p,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr<>'' THEN
          gks_eshop_products.product_descr
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr
          END
      END
    ELSE gks_eshop_products.product_descr
  END as product_descr_p,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr_small<>'' THEN
          gks_eshop_products.product_descr_small
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr_small
          END
      END
    ELSE gks_eshop_products.product_descr_small
  END as product_descr_small_p,
  gks_eshop_products.product_monada_id
  FROM gks_eshop_products
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
  
  where gks_eshop_products.product_disable=0 ";
  $sql.=" and gks_eshop_products.product_class<>'variable'";
  $sql.=' and (';
  if ($term_digit>0) $sql.=" gks_eshop_products.id_product = ".$term_digit." or (";
  
  
  $mywhere='';
  foreach ($term_array as $value) {
    $value_en = greekkeybord($value);
    $mywhere.=" (
    gks_eshop_products.product_code like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_sku like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_object_name like '%".$db_link->escape_string($value)."%' or 
    
    gks_eshop_products.product_code like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_sku like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_object_name like '%".$db_link->escape_string($value_en)."%'
    ) and ";
  } 
  
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")";
  if ($term_digit>0) $sql.=")";
  $sql.=" order by gks_eshop_products.product_code limit 10"; 
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('error sql'));
    echo json_encode($return); die();}

  $html_eidoi='';    
  while ($row = $result->fetch_assoc()) {
    
    $myimg=trim_gks($row['product_photo_p']);
    if ($myimg != '') {
      $myimg='<div class="gks_header_search_results_img"><a href="/my/admin-products-item.php?id='.$row['id_product'].'">'.
      '<img src="'.$myimg.'" border="0" style="max-width:32px;max-height:32px;"/></a></div>';
    }

    $item=
    '<div class="gks_header_search_results_item">'.
      $myimg.
      '<div class="gks_header_search_results_right">'.
        (($term_digit > 0 and $term_digit==$row['id_product']) ? '<b>'.$row['id_product'].'</b>' : $row['id_product']).
        '<br>'.
        '<a href="admin-products-item.php?id='.$row['id_product'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>'.
        '<a href="admin-whi-mov-product-history.php?fproduct_id='.$row['id_product'].'"><i class="fas fa-list-alt" title="'.gks_lang('Καρτέλα').'"></i></a>'.
      '</div>'.
      
      '<a href="admin-products-item.php?id='.$row['id_product'].'">'.$row['product_descr_p'].'</a>'.
      '<div style="clear: both;"></div>'.
    '</div>';
    $html_eidoi.=$item;
  }
  if ($html_eidoi!='') {
      $html.= '<div class="gks_header_search_results_s">'.gks_lang('Είδη').'</div>'.
              $html_eidoi;
  }    
}




if ($html=='') {
  $html='<div class="alert alert-danger" role="alert" style="text-align:center;">'.gks_lang('Δεν βρέθηκαν αποτελέσματα').'<br>'.gks_lang('Δοκιμάστε κάποιον άλλο όρο αναζήτησης').'</div>';
}
$return = array('success' => true, 'message' => base64_encode($html));echo json_encode($return); die();
