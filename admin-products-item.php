<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




db_open();
$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$get_variable_item=0; if (isset($_GET['variable_item'])) $get_variable_item=intval($_GET['variable_item']);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'item']);


if ($id==-1) {
$nav_active_array=array('manage','manage_menu_product','manage_new_product');

  $copy_id=0;
  if (isset($_GET['copy'])) $copy_id=intval($_GET['copy']);
  if ($copy_id > 0) {
    //echo time();
    //die();
    //product_photo
    $myrand= ' copy'.rand(10000,99999);
    $sql="INSERT INTO gks_eshop_products ( 
    product_code,
    product_descr,
    product_def_comments,
    product_sku,
    product_gtin,
    product_upc,
    product_ean,
    product_isbn,
    product_taric,
    product_disable,
    product_class,
    product_photo,
    product_descr_small,product_descr_big,product_can_buy,product_can_sell,product_can_paraxthei,product_monada_id,product_base_type,product_type,
    product_object_name,product_price_retail,
    product_price_retail_sheets_formula,product_price_retail_quantity_formula,product_price,product_price_sheets_formula,
    product_price_quantity_formula,quantitycheck_price,quantitycheck_price_retail,product_price_retail_include_vat,product_price_include_vat,product_is_digital,product_is_simple_download,
    product_need_apostoli,product_fpa_base_id,product_normal,product_need_multi_files,product_need_multi_files_min,product_need_multi_files_max,product_varos,
    product_ogos_x,product_ogos_y,product_ogos_z,product_show_on_dialog,product_sortorder,product_min_pixels_x,product_min_pixels_y,product_min_pixels_can_rotate,
    use_only_mine_ergasies,
    product_price_retail_sale,product_price_retail_sale_from,product_price_retail_sale_to,
    product_price_sale,product_price_sale_from,product_price_sale_to,
    
    product_price_yperx,
    product_price_yperx_sale,
    product_price_yperx_sale_from,
    product_price_yperx_sale_to,
    product_price_yperx_sheets_formula,
    product_price_yperx_quantity_formula,
    quantitycheck_price_yperx,
    product_price_yperx_include_vat,

    product_kostos,min_quantity_alert,def_supplier,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
    
    SELECT
    if (product_code<>'', CONCAT(product_code,'".$myrand."'),'') as product_code_new,
    if (product_descr<>'', CONCAT(product_descr,'".$myrand."'),'') as product_descr_new, 
    product_def_comments,
    if (product_sku<>'', CONCAT(product_sku,'".$myrand."'),'') as product_sku_new, 
    if (product_gtin<>'', CONCAT(product_gtin,'".$myrand."'),'') as product_gtin_new, 
    if (product_upc<>'', CONCAT(product_upc,'".$myrand."'),'') as product_upc_new, 
    if (product_ean<>'', CONCAT(product_ean,'".$myrand."'),'') as product_ean_new, 
    if (product_isbn<>'', CONCAT(product_isbn,'".$myrand."'),'') as product_isbn_new, 
    product_taric,
    product_disable,
    product_class,
    product_photo,
    product_descr_small,product_descr_big,product_can_buy,product_can_sell,product_can_paraxthei,product_monada_id,product_base_type,product_type,
    product_object_name,product_price_retail,
    product_price_retail_sheets_formula,product_price_retail_quantity_formula,product_price,product_price_sheets_formula,
    product_price_quantity_formula,quantitycheck_price,quantitycheck_price_retail,product_price_retail_include_vat,product_price_include_vat,product_is_digital,product_is_simple_download,
    product_need_apostoli,product_fpa_base_id,product_normal,product_need_multi_files,product_need_multi_files_min,product_need_multi_files_max,product_varos,
    product_ogos_x,product_ogos_y,product_ogos_z,product_show_on_dialog,product_sortorder,product_min_pixels_x,product_min_pixels_y,product_min_pixels_can_rotate,
    use_only_mine_ergasies,
    product_price_retail_sale,product_price_retail_sale_from,product_price_retail_sale_to,
    product_price_sale,product_price_sale_from,product_price_sale_to,

    product_price_yperx,
    product_price_yperx_sale,
    product_price_yperx_sale_from,
    product_price_yperx_sale_to,
    product_price_yperx_sheets_formula,
    product_price_yperx_quantity_formula,
    quantitycheck_price_yperx,
    product_price_yperx_include_vat,

    product_kostos,min_quantity_alert,def_supplier,
    now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
    from gks_eshop_products
    WHERE id_product=".$copy_id;
    
    //print '<pre>';
    //print $sql;
    //die();
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $id = $db_link->insert_id;

    $sql="select * from gks_eshop_products_idiotites where product_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $idiotites=array();
    while ($row = $result->fetch_assoc()) {
      $row['new_idiotita_id']=0;
      $row['terms']=array();
      $idiotites[$row['id_eshop_products_idiotites']] = $row;
    }
    $id_eshop_products_idiotites=array_keys($idiotites);

    if (count($id_eshop_products_idiotites)>0) {
      $sql="select * from gks_eshop_products_idiotites_terms where eshop_products_idiotites_id in (".implode(',',$id_eshop_products_idiotites).")";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
      while ($row = $result->fetch_assoc()) {
        $row['new_term_id']=0;
        $idiotites[$row['eshop_products_idiotites_id']]['terms'][$row['id_eshop_products_idiotites_terms']]=$row;
      }
    }
    
    
    foreach ($idiotites as &$idiotita) {
      $sql="insert into gks_eshop_products_idiotites (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        product_id,product_idiotita_id,idiotita_is_variable
      ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id.",".$idiotita['product_idiotita_id'].",".$idiotita['idiotita_is_variable']."
      )";
      //echo $sql;
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
      $idiotita['new_idiotita_id']=$db_link->insert_id;
      
      foreach ($idiotita['terms'] as &$term) {
         $sql="insert into gks_eshop_products_idiotites_terms (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          eshop_products_idiotites_id,product_idiotita_term_id
         ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          ".$idiotita['new_idiotita_id'].",".$term['product_idiotita_term_id']."
         )";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
        $term['new_term_id']=$db_link->insert_id;
         
                 
      }
      unset($term);
      
    }
    unset($idiotita);

    //print '<pre>';print_r($idiotites);die();
    
    
    $sql="select id_product from gks_eshop_products where product_parent_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $var_id_product=array();
    while ($row = $result->fetch_assoc()) {
      $var_id_product[$row['id_product']]=array(
        'id_product' => $row['id_product'],
        'terms'=>array(),
      );
    }
    if (count($var_id_product)>0) {
      $sql="select * from gks_eshop_products_variables where product_id in (".implode(',',array_keys($var_id_product)).")";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
      while ($row = $result->fetch_assoc()) {
        $var_id_product[$row['product_id']]['terms'][]=$row['product_idiotita_term_id'];
      }
    }
    
    
    
    
    //print '<pre>';print_r($idiotites);print_r($var_id_product);die();
    
    foreach ($var_id_product as &$new_variant) {
  
      $sql="INSERT INTO gks_eshop_products ( 
      product_code,
      product_descr,
      product_def_comments,
      product_sku,
      product_gtin,
      product_upc,
      product_ean,
      product_isbn,      
      product_taric,
      product_disable,
      product_parent_id,
      product_class,
      product_descr_variable,
      product_descr_small,product_descr_big,product_can_buy,product_can_sell,product_can_paraxthei,product_monada_id,product_base_type,product_type,
      product_object_name,product_price_retail,
      product_price_retail_sheets_formula,product_price_retail_quantity_formula,product_price,product_price_sheets_formula,
      product_price_quantity_formula,quantitycheck_price,quantitycheck_price_retail,product_price_retail_include_vat,product_price_include_vat,product_is_digital,product_is_simple_download,
      product_need_apostoli,product_fpa_base_id,product_normal,product_need_multi_files,product_need_multi_files_min,product_need_multi_files_max,product_varos,
      product_ogos_x,product_ogos_y,product_ogos_z,product_show_on_dialog,product_sortorder,product_min_pixels_x,product_min_pixels_y,product_min_pixels_can_rotate,
      use_only_mine_ergasies,
      product_price_retail_sale,product_price_retail_sale_from,product_price_retail_sale_to,
      product_price_sale,product_price_sale_from,product_price_sale_to,

      product_price_yperx,
      product_price_yperx_sale,
      product_price_yperx_sale_from,
      product_price_yperx_sale_to,
      product_price_yperx_sheets_formula,
      product_price_yperx_quantity_formula,
      quantitycheck_price_yperx,
      product_price_yperx_include_vat,

      product_kostos,min_quantity_alert,def_supplier,
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
      
      SELECT
      if (product_code<>'', CONCAT(product_code,'".$myrand."'),'') as product_code_new,
      if (product_descr<>'', CONCAT(product_descr,'".$myrand."'),'') as product_descr_new, 
      product_def_comments,
      if (product_sku<>'', CONCAT(product_sku,'".$myrand."'),'') as product_sku_new, 
      if (product_gtin<>'', CONCAT(product_gtin,'".$myrand."'),'') as product_gtin_new, 
      if (product_upc<>'', CONCAT(product_upc,'".$myrand."'),'') as product_upc_new, 
      if (product_ean<>'', CONCAT(product_ean,'".$myrand."'),'') as product_ean_new, 
      if (product_isbn<>'', CONCAT(product_isbn,'".$myrand."'),'') as product_isbn_new, 
      
      product_taric, 
      product_disable,
      ".$id." as product_parent_id_new,
      product_class,
      product_descr_variable,
      product_descr_small,product_descr_big,product_can_buy,product_can_sell,product_can_paraxthei,product_monada_id,product_base_type,product_type,
      product_object_name,product_price_retail,
      product_price_retail_sheets_formula,product_price_retail_quantity_formula,product_price,product_price_sheets_formula,
      product_price_quantity_formula,quantitycheck_price,quantitycheck_price_retail,product_price_retail_include_vat,product_price_include_vat,product_is_digital,product_is_simple_download,
      product_need_apostoli,product_fpa_base_id,product_normal,product_need_multi_files,product_need_multi_files_min,product_need_multi_files_max,product_varos,
      product_ogos_x,product_ogos_y,product_ogos_z,product_show_on_dialog,product_sortorder,product_min_pixels_x,product_min_pixels_y,product_min_pixels_can_rotate,
      use_only_mine_ergasies,
      product_price_retail_sale,product_price_retail_sale_from,product_price_retail_sale_to,
      product_price_sale,product_price_sale_from,product_price_sale_to,

      product_price_yperx,
      product_price_yperx_sale,
      product_price_yperx_sale_from,
      product_price_yperx_sale_to,
      product_price_yperx_sheets_formula,
      product_price_yperx_quantity_formula,
      quantitycheck_price_yperx,
      product_price_yperx_include_vat,
            
      product_kostos,min_quantity_alert,def_supplier,
      now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
      from gks_eshop_products
      WHERE id_product=".$new_variant['id_product'];
      
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
      $variant_id = $db_link->insert_id;
      $new_variant['variant_id']=$variant_id;
      //echo $sql; echo ' '.$variant_id; die();
      foreach ($new_variant['terms'] as $myterm) {
        $sql="insert into gks_eshop_products_variables (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          product_id,product_idiotita_term_id
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          ".$variant_id.",".$myterm."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
      }
    }
    unset($new_variant);
    
    //print '<pre>';print_r($idiotites);print_r($var_id_product);die();
    

    
    





    $product_photo=array();
    $sql="select id_product,product_photo from gks_eshop_products where product_photo<>'' and (id_product=".$copy_id." or product_parent_id=".$copy_id.") order by id_product";
    //echo $sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    while ($row = $result->fetch_assoc()) {
      $product_photo[]=array(
        'id' => $row['id_product'],
        'photo' => $row['product_photo'],
      );
    }
    //print '<pre>';print_r($product_photo);die();
    
    $sql="select * from gks_eshop_products_photo where product_id=".$copy_id." and photo_url<>''";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $photos=array();
    while ($row = $result->fetch_assoc()) {
      $photos[] = $row;
    }
    
    if (count($photos)>0) {
      $mydir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/products/'.$id.'/';
      if (file_exists($mydir)==false) {
        if (@mkdir($mydir , 0755, true) == false ) {
          debug_mail(false,'can not create dir: ',$mydir);
        }  
      }
      if (file_exists($mydir)) {
        foreach ($photos as $value) {
          $filepath=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$value['photo_url'];
          if (file_exists($filepath)) {
            $parts=explode('/', $value['photo_url'],6);
            if (count($parts)==6) {
              $filesubpath=$parts[5];
              $thisdir=$mydir.dirname($filesubpath);
              if (file_exists($thisdir)==false) {
                if (@mkdir($thisdir , 0755, true) == false ) {
                  debug_mail(false,'can not create dir: ',$thisdir);
                }  
              }
              if (file_exists($thisdir)) {
                $dest=$mydir.$filesubpath;
                if (copy($filepath,$dest) == false) {
                  debug_mail(false,'can not copy file: ',$filepath.' to '.$dest);  
                } else {
                  
                  $source_thumb=dirname($filepath).'/thumbnail/'.mb_basename($filepath);
                  $photo_url_new='/my/uploads/products/'.$id.'/'.$filesubpath;
                  $photo_url_thumb=dirname($value['photo_url']).'/thumbnail/'.mb_basename($value['photo_url']);
                  $photo_url_new_thumb=dirname($dest).'/thumbnail/'.mb_basename($dest);
                  $sql="insert into gks_eshop_products_photo (
                  product_id,photo_url,mydate,mysize,user_add_id
                  ) values (
                  ".$id.",'".$db_link->escape_string($photo_url_new)."',now(),".$value['mysize'].",".$my_wp_user_id."
                  )";
                  $result = $db_link->query($sql);        
                  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
                  
                  
                  
                  $thumb_dir=dirname($dest).'/thumbnail';
                  if (file_exists($thumb_dir)==false) {
                    if (@mkdir($thumb_dir , 0755, true) == false ) {
                      debug_mail(false,'can not create dir: ',$thumb_dir);
                    } 
                  }
                  if (file_exists($thumb_dir)) {
                    if (copy($source_thumb,$photo_url_new_thumb)==false) {
                      debug_mail(false,'can not copy file: ',$source_thumb.' to '.$photo_url_new_thumb);  
                    } else {
                      
                      $product_photo_id=0;
                      foreach ($product_photo as $value) {
                        if ($value['photo']==$photo_url_thumb) {
                          $new_id_product=0;
                          if ($value['id'] == $copy_id) { //base product
                            $new_id_product = $id;
                          } else { //variant product
                            foreach ($var_id_product as $value_var) {
                              if ($value_var['id_product']==$value['id']) {
                                $new_id_product=$value_var['variant_id'];
                                break;
                              }
                            } 
                          }
                          
                          if ($new_id_product>0) {
                            //echo $value['photo'];die();
                            $photo_url_thumb=dirname($photo_url_new).'/thumbnail/'.mb_basename($photo_url_new);
                            $sql="update gks_eshop_products set product_photo='".$db_link->escape_string($photo_url_thumb)."' where id_product=".$new_id_product;
                            $result = $db_link->query($sql);        
                            if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
                          }
                          
                          break;
                        }
                      } 
                      
                      
//                      if ($photo_url_thumb==$product_photo) {
//                        $photo_url_thumb=dirname($photo_url_new).'/thumbnail/'.mb_basename($photo_url_new);
//                        $sql="update gks_eshop_products set product_photo='".$db_link->escape_string($photo_url_thumb)."' where id_product=".$id;
//                        $result = $db_link->query($sql);        
//                        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
//                      }
                    }
                  }
                }
                
              }
            }
          }
        }
      } 
    }
    
    
    $sql="insert into gks_eshop_products_categories_products (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    product_category_id,product_id
    )
    select now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    product_category_id,".$id." as product_id
    from gks_eshop_products_categories_products
    where product_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    $sql="insert into gks_production_ergasies_eidos (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    production_ergasia_id,eidos_id
    )
    select now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    production_ergasia_id,".$id." as eidos_id
    from gks_production_ergasies_eidos
    where eidos_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    $sql="insert into gks_eshop_products_brands_products (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    product_brand_id,product_id
    )
    select now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    product_brand_id,".$id." as product_id
    from gks_eshop_products_brands_products
    where product_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    
    //echo '<pre>';print_r($var_id_product);die();
    $sql="insert into gks_eshop_products_prices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    pricelist_id,product_id,
    product_price_plist,product_price_plist_sale,
    product_price_plist_sale_from,product_price_plist_sale_to,
    product_price_plist_sheets_formula,product_price_plist_quantity_formula,
    product_price_plist_include_vat,quantitycheck_price_plist
    )
    select now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    pricelist_id,".$id." as product_id,
    product_price_plist,product_price_plist_sale,
    product_price_plist_sale_from,product_price_plist_sale_to,
    product_price_plist_sheets_formula,product_price_plist_quantity_formula,
    product_price_plist_include_vat,quantitycheck_price_plist
    from gks_eshop_products_prices
    where product_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    
    foreach ($var_id_product as $new_variant) {
      $sql="insert into gks_eshop_products_prices (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      pricelist_id,product_id,
      product_price_plist,product_price_plist_sale,
      product_price_plist_sale_from,product_price_plist_sale_to,
      product_price_plist_sheets_formula,product_price_plist_quantity_formula,
      product_price_plist_include_vat,quantitycheck_price_plist
      )
      select now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
      pricelist_id,".$new_variant['variant_id']." as product_id,
      product_price_plist,product_price_plist_sale,
      product_price_plist_sale_from,product_price_plist_sale_to,
      product_price_plist_sheets_formula,product_price_plist_quantity_formula,
      product_price_plist_include_vat,quantitycheck_price_plist
      from gks_eshop_products_prices
      where product_id=".$new_variant['id_product'];
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}       
      
    } 
    
    //var_dump($id);
    //die();    
    if ($id > 0) {
      header('Location: ?id='.$id);
      die();
    }
  }
  
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';

  $row['id_product']=-1;
  $row['product_parent_id']=0;
  $row['product_class']='simple';
  $row['product_code']='';
  $row['product_photo']='';
  $row['product_descr']='';
  $row['product_def_comments']='';
  $row['product_descr_small']='';
  $row['product_descr_big']='';
  $row['product_can_buy']=0;
  $row['product_can_sell']=1;
  $row['product_base_type']=0;
  $row['product_object_name']='';
  $row['product_price_retail']=1;
  $row['product_price_retail_sale']=0;
  $row['product_price_retail_sheets_formula']='';
  $row['product_price_retail_quantity_formula']='';
  $row['product_price_retail_include_vat']='';

  $row['product_price']=1;
  $row['product_price_sale']=0;
  $row['product_price_sheets_formula']='';
  $row['product_price_quantity_formula']='';
  $row['product_price_include_vat']=0;

  $row['product_price_yperx']=1;
  $row['product_price_yperx_sale']=0;
  $row['product_price_yperx_sheets_formula']='';
  $row['product_price_yperx_quantity_formula']='';
  $row['product_price_yperx_include_vat']=0;
  
 

  $row['product_is_digital']=0;
  $row['product_is_simple_download']=0;
  $row['product_sku']='';
  $row['product_gtin']='';
  $row['product_upc']='';
  $row['product_ean']='';
  $row['product_isbn']='';  
  $row['product_taric']='';
  $row['product_need_apostoli']=1;
  $row['product_fpa_base_id']=1001;
  $row['product_fpa_ejeresi_id']=0;
  $row['aade_katigoria_fpa_ejeresi_descr']='';
  $row['product_monada_id']=1;
  
  $row['product_need_multi_files']=0;
  $row['product_need_multi_files_min']=0;
  $row['product_need_multi_files_max']=0;
  $row['product_varos']=0;
  $row['product_ogos_x']=0;
  $row['product_ogos_y']=0;
  $row['product_ogos_z']=0;
  $row['product_sortorder']=0;
  $row['product_disable']=0;
  $row['product_min_pixels_x']=0;
  $row['product_min_pixels_y']=0;
  $row['product_min_pixels_can_rotate']=0;
  $row['use_only_mine_ergasies']=0;
  $row['product_min_pixels_x']=0;
  $row['product_min_pixels_x']=0;
  
  $row['fpa_base_descr']='';

  
  $row['product_withheldPercentCategory']=0;  
  $row['product_stampDutyPercentCategory']=0;  
  $row['product_feesPercentCategory']=0;  
  $row['product_otherTaxesPercentCategory']=0;  

  $row['product_kostos']=null;
  
  $row['def_supplier']=0;
  $row['gks_nickname_supplier']='';
  $row['min_quantity_alert']=0;
  $row['internal_note']='';
  $row['product_lot_serial']='';
  
  
  $my_page_title=gks_lang('Νέο είδος');    
} else {
  $nav_active_array=array('manage','manage_menu_product','manage_product');


  $sql ="SELECT gks_eshop_products.*,gks_eshop_fpa_base.fpa_base_descr,
  gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_descr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users_supplier.gks_nickname AS gks_nickname_supplier
  FROM ((((gks_eshop_products 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_eshop_products.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_eshop_products.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_fpa_base ON gks_eshop_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
  LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_eshop_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_supplier ON gks_eshop_products.def_supplier = ".GKS_WP_TABLE_PREFIX."users_supplier.ID


  
  where gks_eshop_products.id_product = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Είδος').': '.$row['product_descr'];
  
  
  if ($row['product_class']=='variable_item') {
    if ($row['product_parent_old_id']>0) {
      echo '<html><body>'.
      gks_lang('To συγκεκριμένο είδος ήταν παραλλαγή από το είδος').'<br>'.
      '<a href="admin-products-item.php?id='.$row['product_parent_old_id'].'">'.$row['product_parent_old_id'].'</a><br>'.
      gks_lang('Η συγκεκριμένη παραλλαγή έχει απενεργοποιηθεί').
      '</body></html>';
      die();      
    }
    if ($row['product_parent_id']>0) {
      header('Location: admin-products-item.php?id='.$row['product_parent_id'].'&variable_item='.$id); 
      die();
    }
  }
  
}

$product_price_plist=gks_get_product_price_plist($id);


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$product_class=$row['product_class'];
$product_need_apostoli=$row['product_need_apostoli'];

stat_record();


gks_cache_admin_products_item();

$product_variables=array();
if ($product_class=='variable') {
  $sql_variables="select id_product,product_photo,product_code,
  product_sku,product_gtin,product_upc,product_ean,product_isbn,
  product_taric,product_descr,product_def_comments,product_descr_small,
  product_price,product_price_include_vat,product_price_sale,product_price_sale_from,product_price_sale_to,
  product_price_sheets_formula,product_price_quantity_formula,
  product_price_retail,product_price_retail_include_vat,product_price_retail_sale,product_price_retail_sale_from,product_price_retail_sale_to,
  product_price_retail_sheets_formula,product_price_retail_quantity_formula,
  
  product_price_yperx,
  product_price_yperx_sale,
  product_price_yperx_sale_from,
  product_price_yperx_sale_to,
  product_price_yperx_sheets_formula,
  product_price_yperx_quantity_formula,
  quantitycheck_price_yperx,
  product_price_yperx_include_vat,
        
  product_varos,product_ogos_x,product_ogos_y,product_ogos_z,
  product_fpa_base_id,product_kostos,
  product_variable_sortorder,
  min_quantity_alert
  from gks_eshop_products where product_parent_id=".$id." and product_class='variable_item'
  order by product_variable_sortorder,id_product";
  $result_variables = $db_link->query($sql_variables);        
  if (!$result_variables) {debug_mail(false,'error sql',$sql_variables);die('sql error');}
  $temp_ids=array();
  while ($row_variables = $result_variables->fetch_assoc()) {
    $temp_ids[]=$row_variables['id_product'];
    $row_variables['products_variables']=array();
    $product_variables[$row_variables['id_product']]=$row_variables;
  }  
  if (count($temp_ids)>0) {
    $sql_variables="SELECT gks_eshop_products_variables.product_id, gks_eshop_products_variables.product_idiotita_term_id, gks_product_idiotites.id_product_idiotita, gks_product_idiotites.idiotita_name, gks_product_idiotites_terms.id_product_idiotita_term, gks_product_idiotites_terms.idiotita_term_name
    FROM (gks_eshop_products_variables 
    LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_variables.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term) 
    LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita
    WHERE gks_eshop_products_variables.product_id in (".implode(',',$temp_ids).")
    ORDER BY gks_product_idiotites.idiotita_sortorder, gks_product_idiotites_terms.idiotita_term_sortorder;";  
    $result_variables = $db_link->query($sql_variables);        
    if (!$result_variables) {debug_mail(false,'error sql',$sql_variables);die('sql error');}
    
    while ($row_variables = $result_variables->fetch_assoc()) {
      if (isset($product_variables[$row_variables['product_id']])) {
        $product_variables[$row_variables['product_id']]['products_variables'][]=$row_variables;
      }
    }  
  }
  //print '<pre>';print_r($product_variables);print '</pre>';//die();
}


$lang_data_obj_product=gks_lang_data_obj_prepare('gks_eshop_products','default');
if ($lang_data_obj_product['success']==false) die($lang_data_obj_product['message']);


include_once('_my_header_admin.php');
?>


<link href="css/admin-products-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Είδος');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['product_descr'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Είδος');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>



<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 
          
          <div class="form-group row">
            <label for="product_class" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βασικός τύπος');?>:</label>
            <div class="col-md-8">
              <select id="product_class"  class="form-control form-control-sm myneedsave" style="width:unset;" >
                <option value="simple"   <?php if ($product_class=='simple') echo 'selected';  ?>><?php echo getProductClassDescr('simple');?></option>
                <option value="variable" <?php if ($product_class=='variable') echo 'selected';?>><?php echo getProductClassDescr('variable');?></option>
              </select>
            </div>
          </div> 
          
                           
          <div class="form-group row">
            <label for="product_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="product_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_code']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="product_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="product_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj_product,$row,array('product_descr'));
          ?> 
          
          <div class="form-group row">
            <label for="product_def_comments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο για παραγγελία, παραστατικό, δελτίο');?>:</label>
            <div class="col-md-8">
              <textarea id="product_def_comments" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;"><?php echo htmlspecialchars_gks($row['product_def_comments']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj_product,$row,array('product_def_comments'));
          ?>           
       
<?php if ($GKS_PRODUCT_DESCR_SMALL) {?>          
          <div class="form-group row">
            <label for="product_descr_small" class="col-md-12 col-form-label form-control-sm text-md-right1"><?php echo gks_lang('Μικρή Περιγραφή');?>:</label>
            <div class="col-md-12">
              <textarea id="product_descr_small" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="height:200px;"><?php echo $row['product_descr_small'];?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj_product,$row,array('product_descr_small'));
          ?> 
                    
<?php } ?>
<?php if ($GKS_PRODUCT_DESCR_BIG) {?>          
          <div class="form-group row">
            <label for="product_descr_big" class="col-md-12 col-form-label form-control-sm text-md-right1"><?php echo gks_lang('Μεγάλη Περιγραφή');?>:</label>
            <div class="col-md-12">
              <textarea id="product_descr_big" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="height:200px;"><?php echo $row['product_descr_big'];?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj_product,$row,array('product_descr_big'));
          ?> 
                    
<?php } ?>
          
          
        </div>
      </div>
    


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τιμές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('price');?>>       

          <?php 
          $temp='';
          if ($GKS_ORDERS_PRODUCTION) {
            $sql_sintages="select * from gks_production_bom where bom_product_id=".$id." order by bom_descr";
            $result_sintages = $db_link->query($sql_sintages);        
            if (!$result_sintages) {debug_mail(false,'error sql',$sql_sintages);die('sql error');}
            
            $total_cost=0;
            $ss=0;
            while ($row_sintages = $result_sintages->fetch_assoc()) {
              $ss++;
              $calc_res=array();
              $bom_kostos=0;
              $bom_json=trim_gks($row_sintages['bom_json']);
              if ($bom_json!='') {
                $calc_res=unserialize($bom_json);
                if (isset($calc_res['base']['per_monades'][$row['product_monada_id']])) {
                  $bom_kostos=$calc_res['base']['per_monades'][$row['product_monada_id']];
                  if ($row_sintages['bom_disable']==0) $total_cost+=$bom_kostos;
                }
              }
              
              $temp.='<tr>'.
              '<th class="mytdcm" scope="row" nowrap="">'.$ss.'</th>'.
              '<td class="mytdcm" nowrap><a href="admin-production-bom-item.php?id='.$row_sintages['id_production_bom'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
              '<td class="mytdcml" >'.$row_sintages['bom_descr'].'</td>'.
              '<td class="mytdcm" nowrap>'.($bom_kostos==0 ? '' : myCurrencyFormat($bom_kostos)).'</td>'.
              '<td class="mytdcm" nowrap>'.myimg010r($row_sintages['bom_disable']).'</td>'.
              '</tr>';
            }
            if ($temp!='') {
              $temp=
              '<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
                '<thead>'.
                  '<tr>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> '.
                    '<th class="table-dark" scope="col" style="text-align: left   !important;" width="100%">'.gks_lang('Περιγραφή').'</th> '.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κόστος').'</th>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ενεργή').'</th>'.
                  '</tr>'.
                '</thead>'.
                '<tbody>'.
                $temp.
                '</tbody>'.
              '</table>';
            }
            
            $temp_yliko='';
            $sql_sintages="SELECT gks_production_bom.id_production_bom, 
            gks_production_bom.bom_descr, 
            gks_production_bom.bom_disable, 
            gks_production_bom.bom_kostos,bom_kostos_min,bom_kostos_max
            FROM gks_production_bom_product 
            LEFT JOIN gks_production_bom ON gks_production_bom_product.production_bom_id = gks_production_bom.id_production_bom
            WHERE gks_production_bom_product.pbom_product_id=".$id."
            AND gks_production_bom.id_production_bom Is Not Null
            GROUP BY gks_production_bom.id_production_bom
            ORDER BY gks_production_bom.bom_descr;";
            $result_sintages = $db_link->query($sql_sintages);        
            if (!$result_sintages) {debug_mail(false,'error sql',$sql_sintages);die('sql error');}
            
            $ss=0;
            while ($row_sintages = $result_sintages->fetch_assoc()) {
              $ss++;
              $temp1='';
              if ($row_sintages['bom_kostos']!=0) $temp1= myCurrencyFormat($row_sintages['bom_kostos']);
              $temp2='';
              if ($row_sintages['bom_kostos_min']!=0 or $row_sintages['bom_kostos_max']!=0) {
                if (!($row_sintages['bom_kostos_min']==$row_sintages['bom_kostos'] and $row_sintages['bom_kostos_max']==$row_sintages['bom_kostos'])) {
                  if ($row_sintages['bom_kostos_min']!=0) $temp2=myCurrencyFormat($row_sintages['bom_kostos_min']);
                  if ($row_sintages['bom_kostos_max']!=0) $temp2.= ' &#8767; '.myCurrencyFormat($row_sintages['bom_kostos_max']);
                  if ($temp1!='' and $temp2!='') $temp2='<br>'.$temp2;
                }
              }
              //echo $temp1.$temp2;
              $temp_yliko.='<tr>'.
              '<th class="mytdcm" scope="row" nowrap="">'.$ss.'</th>'.
              '<td class="mytdcm" nowrap><a href="admin-production-bom-item.php?id='.$row_sintages['id_production_bom'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
              '<td class="mytdcml" >'.$row_sintages['bom_descr'].'</td>'.
              '<td class="mytdcm" nowrap>'.$temp1.$temp2.'</td>'.
              '<td class="mytdcm" nowrap>'.myimg010r($row_sintages['bom_disable']).'</td>'.
              '</tr>';
            }
            if ($temp_yliko!='') {
              $temp_yliko=
              '<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
                '<thead>'.
                  '<tr>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> '.
                    '<th class="table-dark" scope="col" style="text-align: left   !important;" width="100%">'.gks_lang('Περιγραφή').'</th> '.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κόστος').'</th>'.
                    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ενεργή').'</th>'.
                  '</tr>'.
                '</thead>'.
                '<tbody>'.
                $temp_yliko.
                '</tbody>'.
              '</table>';
            }
            
          }
          ?>
                        
           
          <div class="form-group row">
            <label for="product_kostos" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Κόστος');?>:</label>
            <div class="col-lg-8">
              <div class="row">
                <div class="col-lg-6">
                  <input id="product_kostos" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_kostos'])) echo myNumberFormatNo0($row['product_kostos']);?>">
                </div>
                <div class="col-lg-5 text-lg-center">
                  <small><?php echo gks_lang('Χωρίς ΦΠΑ','part4','fpa_base_descr');?></small>
                </div>
                
              </div>
              <?php if ($temp!='') {?>
              <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12 gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
                  <button class="btn btn-primary btn-sm set_bom_kostos" data-val="<?php echo myNumberFormatNo0($total_cost);?>" data-to="product_kostos"><?php echo gks_lang('Ορισμός από συνταγές');?></button>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
                    
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

           
          <div class="form-group row">
            <label for="product_price_yperx" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τιμή ΥπερΧονδρικής');?>:</label>
            <div class="col-lg-4">
              <input id="product_price_yperx" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_price_yperx']);?>">
            </div>
            <label for="product_price_yperx_include_vat" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
            <div class="col-lg-2">
              <input type="checkbox" id="product_price_yperx_include_vat" value="1" <?php if ($row['product_price_yperx_include_vat']!=0) echo ' checked '; ?> class="switchery1_this">
              <gkscarddiv data-item="price_y"></gkscarddiv>
            </div>
          </div>
          
          <div class="gks_carddiv_expand" <?php echo gks_card_body('price_y');?>> 
            <div class="form-group row">
              <label for="product_price_yperx_sale" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Προσφορά ΥπερΧονδρικής');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_yperx_sale" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php if ($row['product_price_yperx_sale']!=0) echo myNumberFormatNo0($row['product_price_yperx_sale']);?>">
              </div>
              <label for="product_price_yperx_sale_dates" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
              <div class="col-lg-2">
                <input type="checkbox" id="product_price_yperx_sale_dates" value="1" <?php if (isset($row['product_price_yperx_sale_from']) or isset($row['product_price_yperx_sale_to'])) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
  
            <div class="form-group row" id="div_product_price_yperx_sale_dates" style="<?php if (!(isset($row['product_price_yperx_sale_from']) or isset($row['product_price_yperx_sale_to']))) echo 'display:none;'?>">
              <label for="product_price_yperx_sale_from" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Από');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_yperx_sale_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_yperx_sale_from'])) echo  showDate(strtotime($row['product_price_yperx_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
              <label for="product_price_yperx_sale_to" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Έως');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_yperx_sale_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_yperx_sale_to'])) echo  showDate(strtotime($row['product_price_yperx_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
            </div>
  
  
            <div class="form-group row">
              <label for="product_price_yperx_sheets_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_yperx_sheets_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_yperx_sheets_formula']);?>">
              </div>
              
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[sheets] : <?php echo gks_lang('Σελίδες');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή υπερχονδρικής');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[sheets]*[itemprice]
                <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
                  ></i></div>
            </div>                 
            <div class="form-group row">
              <label for="product_price_yperx_quantity_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_yperx_quantity_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_yperx_quantity_formula']);?>">
              </div>
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή υπερχονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[quantity]*[itemprice]
                <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
                  ></i></div>
            </div>
          </div>
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>


          
          <div class="form-group row">
            <label for="product_price" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τιμή Χονδρικής');?>:</label>
            <div class="col-lg-4">
              <input id="product_price" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_price']);?>">
            </div>
            <label for="product_price_include_vat" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
            <div class="col-lg-2">
              <input type="checkbox" id="product_price_include_vat" value="1" <?php if ($row['product_price_include_vat']!=0) echo ' checked '; ?> class="switchery1_this">
              <gkscarddiv data-item="price_x"></gkscarddiv>
            </div>
          </div>
          <div class="gks_carddiv_expand" <?php echo gks_card_body('price_x');?>> 
            <div class="form-group row">
              <label for="product_price_sale" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Προσφορά Χονδρικής');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_sale" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php if ($row['product_price_sale']!=0) echo myNumberFormatNo0($row['product_price_sale']);?>">
              </div>
              <label for="product_price_sale_dates" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
              <div class="col-lg-2">
                <input type="checkbox" id="product_price_sale_dates" value="1" <?php if (isset($row['product_price_sale_from']) or isset($row['product_price_sale_to'])) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
            <div class="form-group row" id="div_product_price_sale_dates" style="<?php if (!(isset($row['product_price_sale_from']) or isset($row['product_price_sale_to']))) echo 'display:none;'?>">
              <label for="product_price_sale_from" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Από');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_sale_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_sale_from'])) echo  showDate(strtotime($row['product_price_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
              <label for="product_price_sale_to" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Έως');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_sale_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_sale_to'])) echo  showDate(strtotime($row['product_price_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
            </div>
            
            
            <div class="form-group row">
              <label for="product_price_sheets_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_sheets_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_sheets_formula']);?>">
              </div>
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[sheets] : <?php echo gks_lang('Σελίδες');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[sheets]*[itemprice]
                <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
                  ></i></div>
            </div>                 
            <div class="form-group row">
              <label for="product_price_quantity_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_quantity_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_quantity_formula']);?>">
              </div>
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[quantity]*[itemprice]
                <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
                  ></i></div>
            </div>
          </div>
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
         
         
          <div class="form-group row">
            <label for="product_price_retail" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τιμή Λιανικής');?>:</label>
            <div class="col-lg-4">
              <input id="product_price_retail" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_price_retail']);?>">
            </div>
            <label for="product_price_retail_include_vat" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
            <div class="col-lg-2">
              <input type="checkbox" id="product_price_retail_include_vat" value="1" <?php if ($row['product_price_retail_include_vat']!=0) echo ' checked '; ?> class="switchery1_this">
              <gkscarddiv data-item="price_r"></gkscarddiv>
            </div>
          </div>
          <div class="gks_carddiv_expand" <?php echo gks_card_body('price_r');?>> 
            <div class="form-group row">
              <label for="product_price_retail_sale" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Προσφορά Λιανικής');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_retail_sale" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php if ($row['product_price_retail_sale']!=0) echo myNumberFormatNo0($row['product_price_retail_sale']);?>">
              </div>
              <label for="product_price_retail_sale_dates" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
              <div class="col-lg-2">
                <input type="checkbox" id="product_price_retail_sale_dates" value="1" <?php if (isset($row['product_price_retail_sale_from']) or isset($row['product_price_retail_sale_to'])) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
  
            <div class="form-group row" id="div_product_price_retail_sale_dates" style="<?php if (!(isset($row['product_price_retail_sale_from']) or isset($row['product_price_retail_sale_to']))) echo 'display:none;'?>">
              <label for="product_price_retail_sale_from" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Από');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_retail_sale_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_retail_sale_from'])) echo  showDate(strtotime($row['product_price_retail_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
              <label for="product_price_retail_sale_to" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Έως');?>:</label>
              <div class="col-lg-4">
                <input id="product_price_retail_sale_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['product_price_retail_sale_to'])) echo  showDate(strtotime($row['product_price_retail_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
              </div>
            </div>
  
  
            <div class="form-group row">
              <label for="product_price_retail_sheets_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_retail_sheets_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_retail_sheets_formula']);?>">
              </div>
              
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[sheets] : <?php echo gks_lang('Σελίδες');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή λιανικής');?>
                <br>[price] : <?php echo gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[price]*1.5
                <br>[sheets]*[itemprice]
                <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
                  ></i></div>
            </div>                 
            <div class="form-group row">
              <label for="product_price_retail_quantity_formula" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
              <div class="col-lg-7">
                <input id="product_price_retail_quantity_formula" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_price_retail_quantity_formula']);?>">
              </div>
              <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
                <br>[itemprice] : <?php echo gks_lang('η τιμή λιανικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
                <br>[price] : <?php echo gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής');?>
                <br><?php echo gks_lang('π.χ.');?>
                <br>[quantity]*[price]*0.9
                <br>[quantity]*[itemprice]
                <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
                  ></i></div>
            </div>
          </div>
          
          <?php 
          
          foreach ($product_price_plist as $plist) {
            $plist_id='_'.$plist['id_pricelist'];
            if (isset($plist['products'][$id])) {
              $pplist=$plist['products'][$id];  
            } else {
              $pplist=array(
                'product_price_plist'=>0,
                'product_price_plist_sale'=>0,
                'product_price_plist_sale_from'=>'',
                'product_price_plist_sale_to'=>'',
                'product_price_plist_sheets_formula'=>'',
                'product_price_plist_quantity_formula'=>'',
                'product_price_plist_include_vat'=>0,
                'quantitycheck_price_plist'=>'',
              );;
            }
          ?>

          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

          <div class="gks_product_price_plist_item" data-id_pricelist="<?php echo $plist['id_pricelist'];?>">
            <div class="form-group row">
              <label for="product_price_plist<?php echo $plist_id;?>" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo $plist['pricelist_descr'];?>:</label>
              <div class="col-lg-4">
                <input id="product_price_plist<?php echo $plist_id;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($pplist['product_price_plist']);?>">
              </div>
              <label for="product_price_plist_include_vat<?php echo $plist_id;?>" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
              <div class="col-lg-2">
                <input type="checkbox" id="product_price_plist_include_vat<?php echo $plist_id;?>" value="1" <?php if ($pplist['product_price_plist_include_vat']!=0) echo ' checked '; ?> class="switchery1_this">
                <gkscarddiv data-item="<?php echo $plist_id;?>"></gkscarddiv>
              </div>
            </div>
            
            <div class="gks_carddiv_expand" <?php echo gks_card_body($plist_id);?>> 
              <div class="form-group row">
                <label for="product_price_plist_sale<?php echo $plist_id;?>" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Προσφορά').' '.$plist['pricelist_descr'];?>:</label>
                <div class="col-lg-4">
                  <input id="product_price_plist_sale<?php echo $plist_id;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm myneedsave" value="<?php if ($pplist['product_price_plist_sale']!=0) echo myNumberFormatNo0($pplist['product_price_plist_sale']);?>">
                </div>
                <label for="product_price_plist_sale_dates<?php echo $plist_id;?>" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
                <div class="col-lg-2">
                  <input type="checkbox" data-plist_id="<?php echo $plist_id;?>" id="product_price_plist_sale_dates<?php echo $plist_id;?>" value="1" <?php if (!empty($pplist['product_price_plist_sale_from']) or !empty($pplist['product_price_plist_sale_to'])) echo ' checked '; ?> class="switchery1_this">
                </div>
              </div>
    
              <div class="form-group row" id="div_product_price_plist_sale_dates<?php echo $plist_id;?>" style="<?php if (!(!empty($pplist['product_price_plist_sale_from']) or !empty($pplist['product_price_plist_sale_to']))) echo 'display:none;'?>">
                <label for="product_price_plist_sale_from<?php echo $plist_id;?>" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Από');?>:</label>
                <div class="col-lg-4">
                  <input id="product_price_plist_sale_from<?php echo $plist_id;?>" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($pplist['product_price_plist_sale_from'])) echo  showDate(strtotime($pplist['product_price_plist_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
                </div>
                <label for="product_price_plist_sale_to<?php echo $plist_id;?>" class="col-lg-2 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Έως');?>:</label>
                <div class="col-lg-4">
                  <input id="product_price_plist_sale_to<?php echo $plist_id;?>" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($pplist['product_price_plist_sale_to'])) echo  showDate(strtotime($pplist['product_price_plist_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
                </div>
              </div>
    
    
              <div class="form-group row">
                <label for="product_price_plist_sheets_formula<?php echo $plist_id;?>" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
                <div class="col-lg-7">
                  <input id="product_price_plist_sheets_formula<?php echo $plist_id;?>" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pplist['product_price_plist_sheets_formula']);?>">
                </div>
                
                <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                  title="[sheets] : <?php echo gks_lang('Σελίδες');?>
                  <br>[itemprice] : <?php echo gks_lang('η τιμή').' '.$plist['pricelist_descr'];?>
                  <br><?php echo gks_lang('π.χ.');?>
                  <br>[sheets]*[itemprice]
                  <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
                    ></i></div>
              </div>                 
              <div class="form-group row">
                <label for="product_price_plist_quantity_formula<?php echo $plist_id;?>" class="col-lg-4 col-form-label form-control-sm text-lg-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
                <div class="col-lg-7">
                  <input id="product_price_plist_quantity_formula<?php echo $plist_id;?>" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pplist['product_price_plist_quantity_formula']);?>">
                </div>
                <div class="col-lg-1 text-lg-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
                  title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
                  <br>[itemprice] : <?php echo str_replace('[1]',$plist['pricelist_descr'], gks_lang('η τιμή [1] ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου'));?>
                  <br><?php echo gks_lang('π.χ.');?>
                  <br>[quantity]*[itemprice]
                  <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
                    ></i></div>
              </div>
            </div>          
          </div>
          
          <?php }  ?>


        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιδιότητες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('idio');?>>       
          <div class="form-group row">
            <label for="product_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="product_disable"  id="product_disable" value="1" <?php if ($row['product_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="product_can_sell" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μπορεί να πουληθεί');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="product_can_sell" value="1" <?php if ($row['product_can_sell']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="product_can_buy" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μπορεί να αγορασθεί');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="product_can_buy" value="1" <?php if ($row['product_can_buy']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>

          
          
          <div class="form-group row">
            <label for="product_monada_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μονάδα Μέτρησης');?>:</label>
            <div class="col-md-8">
              <select id="product_monada_id"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php 
              $lang_prepare_gks_monades_metrisis=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_monades_metrisis, array('monada_descr','monada_symbol'));
              $sql="SELECT gks_monades_metrisis.id_monada,".
              gks_lang_sql_field('monada_descr',$lang_prepare_gks_monades_metrisis).",".
              gks_lang_sql_field('monada_symbol',$lang_prepare_gks_monades_metrisis)."
              FROM ".$lang_prepare_gks_monades_metrisis['sql']['from1']." gks_monades_metrisis
              ".$lang_prepare_gks_monades_metrisis['sql']['from2']."
              order by monada_sortorder,monada_descr";

              
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_monada'].'" ';
                if ($row_select['id_monada']==$row['product_monada_id']) echo ' selected ';
                echo '>'.$row_select['monada_descr'].' ('.$row_select['monada_symbol'].')</option>';
              }?></select>
            </div>
          </div>
          

          
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" style="height:unset;">
                <input type="radio" name="product_base_type" id="product_base_type0" value="0" <?php if ($row['product_base_type']==0) echo ' checked '; ?>>
                  <label for="product_base_type0"><?php echo gks_product_base_type_descr(0,true);?></label>
                <br>
                <input type="radio" name="product_base_type" id="product_base_type1" value="1" <?php if ($row['product_base_type']==1) echo ' checked '; ?>>
                  <label for="product_base_type1"><?php echo gks_product_base_type_descr(1,true);?></label>
                <br>
                <input type="radio" name="product_base_type" id="product_base_type2" value="2" <?php if ($row['product_base_type']==2) echo ' checked '; ?>>
                  <label for="product_base_type2"><?php echo gks_product_base_type_descr(2,true);?></label>
              </div>  
            </div>
          </div>
          
                
          
          <div class="gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">


            
            <div class="form-group row">
              <label for="product_sku" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('Stock Keeping Unit');?>"><?php echo gks_lang('SKU');?>:</label>
              <div class="col-md-8">
                <input id="product_sku" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_sku']);?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="product_gtin" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('Global Trade Item Number');?>"><?php echo gks_lang('GTIN');?>:</label>
              <div class="col-md-8">
                <input id="product_gtin" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_gtin']);?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="product_upc" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('Universal Product Code');?>"><?php echo gks_lang('UPC');?>:</label>
              <div class="col-md-8">
                <input id="product_upc" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_upc']);?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="product_ean" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('European Article Number');?>"><?php echo gks_lang('EAN');?>:</label>
              <div class="col-md-8">
                <input id="product_ean" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_ean']);?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="product_isbn" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('International Standard Book Number');?>"><?php echo gks_lang('ISBN');?>:</label>
              <div class="col-md-8">
                <input id="product_isbn" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_isbn']);?>">
              </div>
            </div>



            
            <div class="form-group row">
              <label for="product_taric" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Taric No');?>:</label>
              <div class="col-md-8">
                <input id="product_taric" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_taric']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 0710 80 70">
                <i class="product_taric_get_descr fas fa-search-plus" title="<?php echo gks_lang('Αναζήτηση περιγραφής');?>"></i>
                <small>
                  <?php echo gks_lang('Αναζήτηση στο');?> <a href="https://ec.europa.eu/taxation_customs/dds2/taric/taric_consultation.jsp?Lang=el" target="_blank">ec.europa.eu</a> <?php echo gks_lang('ή/και στο');?> <a href="https://www.taxheaven.gr/codes/taric" target="_blank">taxheaven.gr</a>
                </small>
              </div>
              <div class="col-md-12 product_taric_descr"></div>
            </div> 
            
                        
            <div class="form-group row " >
              <label for="product_need_apostoli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρειάζεται αποστολή');?>:</label>
              <div class="col-md-8">
                <input type="checkbox" id="product_need_apostoli" value="1" <?php if ($row['product_need_apostoli']!=0) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
            <div class="form-group row div_product_need_apostoli" style="<?php if ($product_need_apostoli==0) echo 'display:none;';?>">
              <label for="product_varos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βάρος σε gr');?>:</label>
              <div class="col-md-8">
                <input id="product_varos" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_varos'],true);?>" min=0 step="0.01" style="max-width:150px">
              </div>
            </div>
            <div class="form-group row div_product_need_apostoli" style="<?php if ($product_need_apostoli==0) echo 'display:none;';?>">
              <label for="product_ogos_x" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαστάσεις σε cm');?>:</label>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-right:0px;">
                    <label for="product_ogos_x" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;padding-left:0px;"><?php echo gks_lang('Μήκος');?>:</label>
                    <input id="product_ogos_x" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_ogos_x'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
                  </div>                        
                  <div class="col-md-4" style="padding-left:0px;padding-right:0px;">
                    <label for="product_ogos_y" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Πλάτος');?>:</label>
                    <input id="product_ogos_y" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_ogos_y'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
                  </div>
                  <div class="col-md-4" style="padding-left:0px;">
                    <label for="product_ogos_z" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Ύψος');?>:</label>
                    <input id="product_ogos_z" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['product_ogos_z'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
                  </div>
                </div>
              </div>                      
            </div>
            <?php if ($GKS_PRODUCT_LOTS_SERIALS) { ?>
            <div class="form-group row div_product_need_apostoli" style="<?php if ($product_need_apostoli==0) echo 'display:none;';?>">
              <label for="product_lot_serial_null" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παρτίδες - Serial Numbers');?>:</label>
              <div class="col-md-8">
                <div class="form-control-sm" style="height:unset;">
                  <input type="radio" name="product_lot_serial" id="product_lot_serial_null" value="" <?php if (trim_gks($row['product_lot_serial'])=='') echo ' checked '; ?>>
                    <label for="product_lot_serial_null"><?php echo gks_lang('Όχι');?></label>
                  <br>
                  <input type="radio" name="product_lot_serial" id="product_lot_serial_lot" value="lot" <?php if (trim_gks($row['product_lot_serial'])=='lot') echo ' checked '; ?>>
                    <label for="product_lot_serial_lot"><?php echo gks_lang('Παρτίδα');?></label>
                  <br>
                  <input type="radio" name="product_lot_serial" id="product_lot_serial_serial" value="serial" <?php if (trim_gks($row['product_lot_serial'])=='serial') echo ' checked '; ?>>
                    <label for="product_lot_serial_serial"><?php echo gks_lang('Serial Number');?></label>
                </div>  
              </div>            
            
            </div>
            
            <?php } ?>
            
          </div>
                            
          <div class="gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">
            <div class="form-group row">
              <label for="product_is_digital" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ψηφιακό');?>:</label>
              <div class="col-md-8">
                <input type="checkbox" id="product_is_digital" value="1" <?php if ($row['product_is_digital']!=0) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
            <div class="form-group row" id="div_product_is_simple_download" style="<?php if ($row['product_is_digital']==0) echo 'display:none;';?>">
              <label for="product_is_simple_download" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απλό Download');?>:</label>
              <div class="col-md-8">
                <input type="checkbox" id="product_is_simple_download" value="1" <?php if ($row['product_is_simple_download']!=0) echo ' checked '; ?> class="switchery1_this">
              </div>
            </div>
          </div>
          <div class="gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
            <div class="form-group row">
              <label for="product_need_multi_files" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απαιτούνται αρχεία');?>:</label>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4">
                    <input type="checkbox" id="product_need_multi_files" value="1" <?php if ($row['product_need_multi_files']!=0) echo ' checked '; ?> class="switchery1_this">
                  </div>                        
                  <div class="col-md-4 div_product_need_multi_files" style="<?php if ($row['product_need_multi_files']==0) echo 'display:none;';?>">
                    <label for="product_need_multi_files_min" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Από');?>:</label>
                    <input id="product_need_multi_files_min" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['product_need_multi_files_min'];?>" style="display: inline;max-width: 70px;" min="0">
                  </div>
                  <div class="col-md-4 div_product_need_multi_files" style="<?php if ($row['product_need_multi_files']==0) echo 'display:none;';?>">
                    <label for="product_need_multi_files_max" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Έως');?>:</label>
                    <input id="product_need_multi_files_max" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['product_need_multi_files_max'];?>" style="display: inline;max-width: 70px;" min="0">
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group row div_product_need_multi_files" style="<?php if ($row['product_need_multi_files']==0) echo 'display:none;';?>">
              <label for="product_object_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα Αντικειμένου');?>:</label>
              <div class="col-md-8">
                <input id="product_object_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_object_name']);?>">
                <small><i><?php echo gks_lang('Εισάγετε το [[]] για να αντικατασταθεί με τον αύξον αριθμό');?></i></small>
              </div>
            </div>
            
          </div>
          <div class="form-group row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
            <label for="product_varos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ελάχιστα Pixels');?>:</label>
            <div class="col-md-8">
              <div class="row">
                <div class="col-md-6">
                  <label for="product_min_pixels_x" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;">Χ:</label>
                  <input id="product_min_pixels_x" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['product_min_pixels_x'];?>" style="display: inline;max-width: 70px;" min="0" >
                </div>                        
                <div class="col-md-6">
                  <label for="product_min_pixels_y" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;">Y:</label>
                  <input id="product_min_pixels_y" type="number" class="form-control form-control-sm" value="<?php echo $row['product_min_pixels_y'];?>" style="display: inline;max-width: 70px;" min="0">
                </div>
              </div>
            </div>                      
          </div>
          <div class="form-group row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
            <label for="product_min_pixels_can_rotate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μπορεί να περιστραφεί το προϊόν');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="product_min_pixels_can_rotate" value="1" <?php if ($row['product_min_pixels_can_rotate']!=0) echo ' checked '; ?> class="switchery1_this">
              <br><small><i><?php echo gks_lang('π.χ. η εκτύπωση 13x18 μπορεί να περιστραφεί, η εκτύπωση σε κούπα δεν μπορεί');?></i></small>
            </div>
          </div>  

          <div class="form-group row gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">
            <label for="min_quantity_alert" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όριο αποθέματος');?>:</label>
            <div class="col-md-4">
              <input id="min_quantity_alert" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['min_quantity_alert']!=0) echo $row['min_quantity_alert'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>">
            </div>
          </div>
          <div class="form-group row gks_base_type0" style="<?php if (!($row['product_base_type']==0)) echo 'display:none;';?>">
            <label for="def_supplier" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προμηθευτής');?>:</label>
            <div class="col-md-4">
              <input id="def_supplier" type="text" class="form-control form-control-sm myneedsave" data-id="<?php echo intval($row['def_supplier']);?>" value="<?php echo $row['gks_nickname_supplier'];?>">
            </div>
          </div>


          <?php if ($GKS_ORDERS_PRODUCTION) { ?>
          <div class="form-group row gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνταγές');?>:</label>
            <div class="col-md-8">
              <div class="row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
                <div class="col-md-12">
                  <div style="font-size: 0.875rem;padding: 0.25rem;"><?php echo gks_lang('Ως παραγόμενο');?>:</div>
                  <?php echo $temp;?>
                </div>
              </div>
              <div class="row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?><?php if ($temp!='') echo 'margin-top: 10px;'?>" >
                <div class="col-md-12">
                  <a class="btn btn-primary btn-sm" href="admin-production-bom-item.php?id=-1&product_id=<?php echo $id;?>"><?php echo gks_lang('Προσθήκη συνταγής');?></a>
                </div>
              </div>
              <?php if (1==1 or $temp_yliko!='') { ?>
              <div class="row gks_base_type0 gks_base_type1 gks_base_type1_pt12" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?><?php if ($row['product_base_type']==1) echo 'padding-top: 12px;';?>">
                <div class="col-md-12">
                  <div style="font-size: 0.875rem;padding: 0.25rem;"><?php echo gks_lang('Ως υλικό');?>:</div>
                  <?php echo $temp_yliko;?>
                </div>
              </div>              
              <?php } ?>
            </div>
            
          </div>
          <?php } ?>          
<!--
          <div class="form-group row">
            <label for="product_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="product_sortorder" type="text" class="form-control form-control-sm" value="<?php echo $row['product_sortorder'];?>"  style="max-width:150px">
            </div>
          </div>
-->          
                       
        </div>
      </div>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('log');?>>
          <div class="form-group row">
            <label for="product_fpa_base_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ');?>:</label>
            <div class="col-md-8">
              <select id="product_fpa_base_id"  class="form-control form-control-sm myneedsave gks_select2" style="max-width:200px">
              <option value="0"></option>
              <?php
              $sql="SELECT id_fpa_base,fpa_base_descr
              FROM gks_eshop_fpa_base
              WHERE fpa_base_disable=0
              ORDER BY fpa_base_sortorder";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              $product_fpa_base_id_array=array();
              while ($row_select = $result_select->fetch_assoc()) {
                $product_fpa_base_id_array[]=$row_select;
              }
              foreach ($product_fpa_base_id_array as $row_select) {
                echo '<option value="'.$row_select['id_fpa_base'].'" ';
                if ($row_select['id_fpa_base']==$row['product_fpa_base_id']) echo ' selected ';
                echo '>'.gks_lang($row_select['fpa_base_descr'].'','part4','fpa_base_descr').'</option>';
              }?></select>
            </div>
          </div>
          
          <div class="form-group row" id="div_product_fpa_ejeresi_id" style="<?php if ($row['product_fpa_base_id']!=1004) echo 'display:none;';?>">
            <label for="product_fpa_ejeresi_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτία Εξαίρεσης ΦΠΑ');?>:</label>
            <div class="col-md-8">
              <select id="product_fpa_ejeresi_id"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_katigoria_fpa_ejeresi ORDER BY sortorder,aade_katigoria_fpa_ejeresi_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_katigoria_fpa_ejeresi'].'" ';
                if ($row_select['id_aade_katigoria_fpa_ejeresi']==$row['product_fpa_ejeresi_id']) echo ' selected ';
                echo '>'.$row_select['aade_katigoria_fpa_ejeresi_descr'].'</option>';
              }?></select>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="product_withheldPercentCategory" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φόροι Παρακρατούμενοι');?>:</label>
            <div class="col-md-8">
              <select id="product_withheldPercentCategory"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_katigoria_parakratoumemenon_foron ORDER BY sortorder,aade_katigoria_parakratoumemenon_foron_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_katigoria_parakratoumemenon_foron'].'" ';
                if ($row_select['id_aade_katigoria_parakratoumemenon_foron']==$row['product_withheldPercentCategory']) echo ' selected ';
                echo '>'.$row_select['aade_katigoria_parakratoumemenon_foron_descr'].'</option>';
              }?></select>
            </div>
          </div>
          <div class="form-group row">
            <label for="product_otherTaxesPercentCategory" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Λοιποί Φόροι');?>:</label>
            <div class="col-md-8">
              <select id="product_otherTaxesPercentCategory"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_katigoria_loipon_foron where aade_disable=0 ORDER BY sortorder,aade_katigoria_loipon_foron_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_katigoria_loipon_foron'].'" ';
                if ($row_select['id_aade_katigoria_loipon_foron']==$row['product_otherTaxesPercentCategory']) echo ' selected ';
                echo '>'.$row_select['aade_katigoria_loipon_foron_descr'].'</option>';
              }?></select>
            </div>
          </div>          
          <div class="form-group row">
            <label for="product_stampDutyPercentCategory" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>:</label>
            <div class="col-md-8">
              <select id="product_stampDutyPercentCategory"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_katigoria_xartosimou ORDER BY sortorder,aade_katigoria_xartosimou_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_katigoria_xartosimou'].'" ';
                if ($row_select['id_aade_katigoria_xartosimou']==$row['product_stampDutyPercentCategory']) echo ' selected ';
                echo '>'.$row_select['aade_katigoria_xartosimou_descr'].'</option>';
              }?></select>
            </div>
          </div>          
          <div class="form-group row">
            <label for="product_feesPercentCategory" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τέλη');?>:</label>
            <div class="col-md-8">
              <select id="product_feesPercentCategory"  class="form-control form-control-sm myneedsave gks_select2" >
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_aade_katigoria_telon ORDER BY sortorder,aade_katigoria_telon_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_aade_katigoria_telon'].'" ';
                if ($row_select['id_aade_katigoria_telon']==$row['product_feesPercentCategory']) echo ' selected ';
                echo '>'.$row_select['aade_katigoria_telon_descr'].'</option>';
              }?></select>
            </div>
          </div>          
          
          <div id="xarakt_table">
            <?php 
            $out_xarakt_esoda=array();
            $out_xarakt_eksoda=array();
            
            $sql_income="select
            acc_eidos_parastatikou_id as ep_id,
            aade_typos_xarakt_esodon_id as typos_id,
            aade_katigoria_xarakt_esodon_id as cat_id,
            acc_inv_product_income_pososto as pososto
            from gks_eshop_products_income
            where product_id in (".$id.")
            order by id_product_income";
            $result_income = $db_link->query($sql_income);        
            if (!$result_income) {debug_mail(false,'error sql',$sql_income); die('sql error');}
            while ($row_income = $result_income->fetch_assoc()) {
              $out_xarakt_esoda[]=$row_income;
            }
                      
            $sql_expenses="select
            acc_eidos_parastatikou_id as ep_id,
            aade_typos_xarakt_eksodon_id as typos_id,
            aade_katigoria_xarakt_eksodon_id as cat_id,
            acc_inv_product_expenses_pososto as pososto
            from gks_eshop_products_expenses
            where product_id in (".$id.")
            order by id_product_expenses";
            $result_expenses = $db_link->query($sql_expenses);        
            if (!$result_expenses) {debug_mail(false,'error sql',$sql_expenses); die('sql error');}
            while ($row_expenses = $result_expenses->fetch_assoc()) {
              $out_xarakt_eksoda[]=$row_expenses;
            }
                      
            ?>
  
            <div class="form-group row div_add_xarakt_esoda" style="margin-top: 20px;">
              <div class="col-md-11 text-center" style="background-color1: rgba(0, 0, 0, 0.03);border-radius: 10px 10px 0px 0px;border1: 1px solid #bbbbbb;">
                <?php echo gks_lang('Χαρακτηρισμός Εσόδων');?>
              </div>
              <div class="col-md-1 text-center">
                <i class="fas fa-plus-circle gks_add_xarakt_esoda"  data-aa="-1" style="<?php echo (count($out_xarakt_esoda) > 0 ? 'display:none;' : '');?>"></i>
              </div>            
            </div>
  <?php
            $xx=0;
            $span_sum_xarakt=0;
            foreach ($out_xarakt_esoda as $xarakt) { 
              $xx++;
              $span_sum_xarakt+=$xarakt['pososto'];
                ?>
            <div class="form-group row div_gks_xarakt_esoda" style="margin: 0px 0px 10px 0px; border-bottom: 1px solid lightblue;padding-bottom: 10px;" data-xx="<?php echo $xx;?>">
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['ep_id'];?>" class="gks_xarakt_esoda_eidos_parastatikou_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['cat_id'];?>" class="gks_xarakt_esoda_cat_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              
              
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['typos_id'];?>" class="gks_xarakt_esoda_typos_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              
              
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >
                <input type="number" class="gks_xarakt_esoda_ammount form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>"
                value="<?php if ($xarakt['pososto']!=0) echo number_format($xarakt['pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" placeholder="%"
                style="text-align:right;" min=0 step="1">
              </div>
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-center offset-md-0 offset-lg-0 offset-xl-0">
                <i class="fas fa-clone gks_clone_eidos_xarakt_esoda" data-xx="<?php echo $xx;?>" style=""></i>
                <i class="fas fa-trash-alt gks_delete_eidos_xarakt_esoda" data-xx="<?php echo $xx;?>" style=""></i>
                <i class="fas fa-plus-circle gks_add_xarakt_esoda" style="<?php echo ($xx < count($out_xarakt_esoda) ? 'display:none;' : '');?>"></i>
              </div>
            </div>
  <?php     } ?>                
            <div class="form-group row div_sum_xarakt_esoda" style="margin: 0px;<?php echo (count($out_xarakt_esoda) == 0 ? 'display:none;' : '');?>">
              <div class="col-6 col-md-6 col-lg-6 col-xl-6 gks_items_col text-right gks_eidos_extra_label div_gks_xarakt_esoda_title">
                <?php echo gks_lang('Χαρακτηρισμένο ποσοστό');?>:
              </div>
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-lg-right text-xl-right text1-md-left gks_eidos_extra_label">
                <span class="span_sum_xarakt_esoda"><?php echo myCurrencyFormat($span_sum_xarakt,false);?></span>%
              </div>
            </div>          
  
  
  
  
            <div class="form-group row div_add_xarakt_eksoda" style="margin-top: 20px;">
              <div class="col-md-11 text-center" style="background-color1: rgba(0, 0, 0, 0.03);border-radius: 10px 10px 0px 0px;border1: 1px solid #bbbbbb;">
                <?php echo gks_lang('Χαρακτηρισμός Εξόδων');?>:
              </div>
              <div class="col-md-1 text-center">
                <i class="fas fa-plus-circle gks_add_xarakt_eksoda" data-aa="-1" style="<?php echo (count($out_xarakt_eksoda) > 0 ? 'display:none;' : '');?>"></i>
              </div>            
            </div>
  <?php
            $xx=0;
            $span_sum_xarakt=0;
            foreach ($out_xarakt_eksoda as $xarakt) { 
              $xx++;
              $span_sum_xarakt+=$xarakt['pososto'];
                ?>
            <div class="form-group row div_gks_xarakt_eksoda" style="margin: 0px 0px 10px 0px; border-bottom: 1px solid lightblue;padding-bottom: 10px;" data-xx="<?php echo $xx;?>">
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['ep_id'];?>" class="gks_xarakt_eksoda_eidos_parastatikou_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['cat_id'];?>" class="gks_xarakt_eksoda_cat_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              <div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >
                <select data-dbval="<?php echo $xarakt['typos_id'];?>" class="gks_xarakt_eksoda_typos_id form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>" style="width:100%;">
                </select>
              </div>
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >
                <input type="number" class="gks_xarakt_eksoda_ammount form-control form-control-sm myneedsave" data-xx="<?php echo $xx;?>"
                value="<?php if ($xarakt['pososto']!=0) echo number_format($xarakt['pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" placeholder="%"
                style="text-align:right;" min=0 step="1">
              </div>
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-center offset-md-0 offset-lg-0 offset-xl-0">
                <i class="fas fa-clone gks_clone_eidos_xarakt_eksoda" data-xx="<?php echo $xx;?>" style=""></i>
                <i class="fas fa-trash-alt gks_delete_eidos_xarakt_eksoda" data-xx="<?php echo $xx;?>" style=""></i>
                <i class="fas fa-plus-circle gks_add_xarakt_eksoda" style="<?php echo ($xx < count($out_xarakt_eksoda) ? 'display:none;' : '');?>"></i>
              </div>
            </div>
  <?php     } ?>            
            <div class="form-group row div_sum_xarakt_eksoda" style="margin: 0px;<?php echo (count($out_xarakt_eksoda) == 0 ? 'display:none;' : '');?>">
              <div class="col-6 col-md-6 col-lg-6 col-xl-6 gks_items_col text-right gks_eidos_extra_label div_gks_xarakt_eksoda_title">
                <?php echo gks_lang('Χαρακτηρισμένο Ποσοστό');?>:
              </div>
              <div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-lg-right text-xl-right text1-md-left gks_eidos_extra_label">
                <span class="span_sum_xarakt_eksoda"><?php echo myCurrencyFormat($span_sum_xarakt,false);?></span>%
              </div>
            </div> 
                    
          </div>
          
        </div>
      </div>

    </div>
                      
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φωτογραφίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('photo');?>>       
          <div class="row">
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία του είδους');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              $user_photo_value="";
              $myimgurl = $row['product_photo']; //get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
              //echo $myimgurl;
              if ($myimgurl.'' == '') {
                $myimgurl="/my/img/product.png";
              } else {
                $user_photo_value = $myimgurl;
              }
              ?>
              <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_product_photo_img"/><br>
              
              <a href="" id="reset_profile_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                if ($user_photo_value == '') {
                  echo ' style="display:none" ';
                }
                ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
              <br><input type="hidden" id="form_product_photo" name="form_product_photo" value="<?php echo $user_photo_value;?>" />
            </div>                     
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες του είδους');?></div>
            
            <form role="form" method="post" action="admin-products-item-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="product_id" id="product_id" value="<?php echo $id;?>">
              <div id="lightgallery_user">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_eshop_products_photo where product_id=".$id." and filesobjectlist=0 order by id_product_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_product_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_product_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('gks_eshop_products',$id);?>
            </form>                      
            
            
          </div>

        </div>
      </div>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Ιδιότητες');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="idiotites_add" style="margin-left: 10px;"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('idio');?> id="div_idiotites">       
          <?php
          $sql_idiotites="SELECT gks_eshop_products_idiotites.*, 
          gks_product_idiotites.id_product_idiotita,gks_product_idiotites.idiotita_name, gks_product_idiotites.idiotita_descr, gks_product_idiotites.idiotita_type
          FROM gks_eshop_products_idiotites 
          LEFT JOIN gks_product_idiotites ON gks_eshop_products_idiotites.product_idiotita_id = gks_product_idiotites.id_product_idiotita
          WHERE gks_eshop_products_idiotites.product_id=".$id."
          and id_product_idiotita is not null
          ORDER BY gks_product_idiotites.idiotita_sortorder;";
          $result_idiotites = $db_link->query($sql_idiotites);        
          if (!$result_idiotites) {debug_mail(false,'error sql',$sql_idiotites);die('sql error');}
          $idiotites=array();
          $idiotites_ids=array();
          while ($row_idiotites = $result_idiotites->fetch_assoc()) {
            $idiotites_ids[]=$row_idiotites['id_eshop_products_idiotites'];
            $idiotites[$row_idiotites['id_eshop_products_idiotites']]=array(
              'id_eshop_products_idiotites'=>$row_idiotites['id_eshop_products_idiotites'],
              'idiotita_is_variable' => $row_idiotites['idiotita_is_variable'],
              'id_product_idiotita' => $row_idiotites['product_idiotita_id'],
              'idiotita_name' => $row_idiotites['idiotita_name'],
              'idiotita_type' => $row_idiotites['idiotita_type'],
              'terms'=>array(),
            );
          }
          
          if (count($idiotites_ids)>0) {
            $sql_idiotites="SELECT gks_eshop_products_idiotites_terms.eshop_products_idiotites_id, 
            gks_eshop_products_idiotites_terms.product_idiotita_term_id, gks_product_idiotites_terms.id_product_idiotita_term, 
            gks_product_idiotites_terms.idiotita_term_name, gks_product_idiotites_terms.idiotita_term_descr, 
            gks_product_idiotites_terms.idiotita_term_button, gks_product_idiotites_terms.idiotita_term_color, gks_product_idiotites_terms.idiotita_term_image
            FROM gks_eshop_products_idiotites_terms 
            LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_idiotites_terms.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term
            WHERE gks_eshop_products_idiotites_terms.eshop_products_idiotites_id In (".implode(',',$idiotites_ids).")
            ORDER BY gks_product_idiotites_terms.idiotita_term_sortorder";
            $result_idiotites = $db_link->query($sql_idiotites);        
            if (!$result_idiotites) {debug_mail(false,'error sql',$sql_idiotites);die('sql error');}
  
            while ($row_idiotites = $result_idiotites->fetch_assoc()) {
              if (isset($idiotites[$row_idiotites['eshop_products_idiotites_id']])) {
                $idiotites[$row_idiotites['eshop_products_idiotites_id']]['terms'][] =array(
                  'id_product_idiotita_term' => $row_idiotites['id_product_idiotita_term'],
                  'idiotita_term_name' => $row_idiotites['idiotita_term_name'],
                  'idiotita_term_button' => $row_idiotites['idiotita_term_button'],
                  'idiotita_term_color' => $row_idiotites['idiotita_term_color'],
                  'idiotita_term_image' => $row_idiotites['idiotita_term_image'],
                );
              }
            }
          }
          
          
          

          foreach ($idiotites as $idiotita) {
            $temp=array();
            foreach ($idiotita['terms'] as $term) {
              $temp[]=$term['idiotita_term_name'];
            }
            $terms=implode(']][[',$temp);
            echo '<div class="form-group row div_gks_idiotita" data-id="'.$idiotita['id_product_idiotita'].'">'.
              '<label class="col-md-4 col-form-label form-control-sm text-md-right">'.$idiotita['idiotita_name'].'</label>'.
              '<div class="col-md-7">'.
                '<input class="gks_idiotita form-control form-control-sm myneedsave" data-id="'.$idiotita['id_product_idiotita'].'" type="text" value="'.
                  $terms.
                  '">'.
                '<small class="small_gks_idiotita_isv" style="'.
                ($product_class=='variable' ? '' : 'display:none;').
                '"><input class="gks_idiotita_isv" data-id="'.$idiotita['id_product_idiotita'].'" type="checkbox" value="1" '.($idiotita['idiotita_is_variable']!=0 ? 'checked' : '').'>'.
                  ' '.gks_lang('Χρησιμοποιείται στις παραλλαγές').'</small>'.
              '</div>'.
              '<div class="col-md-1">'.
              '<i class="fas fa-trash-alt gks_idiotita_delete" data-id="'.$idiotita['id_product_idiotita'].'" style=""></i>'.
              '</div>'.
            '</div>';
          }
          //echo '<pre>'.print_r($idiotites,true).'</pre>';
          ?>

        </div>
      </div>
      
      <div class="card gks_card_expand" id="div_variables" style="<?php if ($product_class!='variable') echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Παραλλαγές');?></span>
          <button type="button" class="btn btn-sm btn-primary gks_stoppropagation" id="variable_add" style="margin-left: 10px;"><?php echo gks_lang('Προσθήκη');?></button>
          <button type="button" class="btn btn-sm btn-primary gks_stoppropagation" id="variable_actions" style="margin-left: 10px;"><?php echo gks_lang('Ενέργειες');?></button>
          <button type="button" class="btn btn-sm btn-primary gks_stoppropagation" id="variable_list" style="margin-left: 10px;"><?php echo gks_lang('Λίστα');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('idio', ($get_variable_item>0) );?> >  

          
          <div id="div_variables_list">    
        <?php
        $paa=0;
        foreach ($product_variables as $variable_item) {
          $paa++;
          ?>

          <div class="variable_product card gks_card_expand" data-pid="<?php echo $variable_item['id_product'];?>" data-paa="<?php echo $paa;?>">
            <div class="card-header" style="text-align:left;padding-right: 60px;">
              <span class="variables_id">#<?php echo $variable_item['id_product'];?></span>
              <span class="variables_list_combos">
              <?php
              foreach ($idiotites as $value1) {
                if ($value1['idiotita_is_variable']==1) {
                  echo '<select class="variables_combo form-control form-control-sm myneedsave gks_stoppropagation" data-iid="'.$value1['id_product_idiotita'].'">';
                  echo '<option value="0">'.gks_lang('Οποιοδήποτε').'</option>';
                  foreach ($value1['terms'] as $value2) {
                    echo '<option value="'.$value2['id_product_idiotita_term'].'"';
                    foreach ($variable_item['products_variables'] as $value3) {
                      if ($value3['product_idiotita_term_id'] == $value2['id_product_idiotita_term']) {
                        echo ' selected';
                        //break;
                      }
                    } 
                    echo '>'.$value2['idiotita_term_name'].'</option>';
                  } 
                  echo '</select>';
                  
                }
                //echo '<pre>';print_r($value1);echo '</pre>';//die(); 
              } 
              ?>
              </span>
              <i class="variable_product_delete fas fa-trash-alt gks_stoppropagation"></i>
              <i class="fas fa-arrows-alt-v sortorder_handle"></i>
            </div>
            <?php
            $this_display_none='display:none;';
            if ($get_variable_item>0 and $get_variable_item==$variable_item['id_product']) $this_display_none='';
            ?>
            <div class="card-body" style="<?php echo $this_display_none;?>">       

              <div class="col-md-12" style="text-align:center;margin-bottom: 10px;">
                <?php
                $curr_photo_value='';
                $myimgurl = trim_gks($variable_item['product_photo']); //trim_gks($row['product_photo']); 
                if ($myimgurl.'' == '') {
                  $myimgurl="/my/img/product.png";
                } else {
                  $curr_photo_value = $myimgurl;
                }
                ?>
                <img src="<?php echo $myimgurl;?>" border="0" style="" data-paa="<?php echo $paa;?>" class="variable_product_photo_img"/><br>
                <img src="/my/img/0.png" data-paa="<?php echo $paa;?>" class="variable_product_photo_reset" title="<?php echo gks_lang('Αφαίρεση');?>"
                style="<?php echo ($curr_photo_value=='' ? 'display:none;' : '');?>">
                <input type="hidden" data-paa="<?php echo $paa;?>" class="variable_product_photo" value="<?php echo $curr_photo_value;?>" />
              </div>
                            
              <div class="form-group row">
                <label for="variable_product_code_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>s:</label>
                <div class="col-md-8">
                  <input id="variable_product_code_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_code form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_code']);?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="variable_product_descr_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_descr_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_descr form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_descr']);?>">
                </div>
              </div> 
              <?php 
              echo gks_lang_data_obj_render_html($lang_data_obj_product,$variable_item,array('product_descr'), true, 'laang'.$paa,'_variable');
              ?>               

              <div class="form-group row">
                <label for="variable_product_def_comments_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο για παραγγελία, παραστατικό, δελτίο');?>:</label>
                <div class="col-md-8">
                  <textarea id="variable_product_def_comments_<?php echo $paa;?>" type="text" class="variable_product_def_comments form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;"><?php echo htmlspecialchars_gks($variable_item['product_def_comments']);?></textarea>
                </div>
              </div> 
              <?php 
              echo gks_lang_data_obj_render_html($lang_data_obj_product,$variable_item,array('product_def_comments'), true, 'laang'.$paa,'_variable');
              ?>               

              
              
<?php if ($GKS_PRODUCT_DESCR_SMALL) {?>              
              <div class="form-group row">
                <label for="variable_product_descr_small_<?php echo $paa;?>" class="col-md-12 col-form-label form-control-sm text-md-right1"><?php echo gks_lang('Μικρή Περιγραφή');?>:</label>
                <div class="col-md-12">
                  <textarea id="variable_product_descr_small_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_descr_small form-control form-control-sm myneedsave"><?php echo htmlspecialchars_gks($variable_item['product_descr_small']);?></textarea>
                </div>
              </div>
              <?php 
              echo gks_lang_data_obj_render_html($lang_data_obj_product,$variable_item,array('product_descr_small'), true, 'laang'.$paa,'_variable');
              ?>  
                            
<?php } ?>              
              <div class="form-group row">
                <label for="variable_product_sku_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('Stock Keeping Unit');?>"><?php echo gks_lang('SKU');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_sku_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_sku form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_sku']);?>">
                </div>
              </div> 
              <div class="form-group row">
                <label for="variable_product_gtin_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" title="Global Trade Item Number"><?php echo gks_lang('GTIN');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_gtin_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_gtin form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_gtin']);?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="variable_product_upc_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('Universal Product Code');?>"><?php echo gks_lang('UPC');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_upc_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_upc form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_upc']);?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="variable_product_ean_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('European Article Number');?>"><?php echo gks_lang('EAN');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_ean_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_ean form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_ean']);?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="variable_product_isbn_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" title="<?php echo gks_lang('International Standard Book Number');?>"><?php echo gks_lang('ISBN');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_isbn_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_isbn form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_isbn']);?>">
                </div>
              </div>
              
              
               
              <div class="form-group row">
                <label for="variable_product_taric_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Taric No');?>:</label>
                <div class="col-md-8">
                  <input id="variable_product_taric_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_taric form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_taric']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 0710 80 70">
                  <i class="product_taric_get_descr fas fa-search-plus" title="<?php echo gks_lang('Αναζήτηση περιγραφής');?>"></i>
                </div>
                <div class="col-md-12 product_taric_descr"></div>
              </div>  
              
                       
              <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
              


<?php 
$temp='';
if ($GKS_ORDERS_PRODUCTION) {
  $sql_sintages="select * from gks_production_bom where bom_product_id=".$id." or bom_product_id=".$variable_item['id_product']." order by bom_descr";
  $result_sintages = $db_link->query($sql_sintages);        
  if (!$result_sintages) {debug_mail(false,'error sql',$sql_sintages);die('sql error');}
  
  $total_cost=0;
  $ss=0;
  while ($row_sintages = $result_sintages->fetch_assoc()) {
    $ss++;
    $calc_res=array();
    $bom_kostos=0;
    $bom_json=trim_gks($row_sintages['bom_json']);
    if ($bom_json!='') {
      $calc_res=unserialize($bom_json);
      
      if (isset($calc_res['product_variants'][$variable_item['id_product']])) {
        if (isset($calc_res['product_variants'][$variable_item['id_product']]['per_monades'][$row['product_monada_id']])) {
          $bom_kostos=$calc_res['product_variants'][$variable_item['id_product']]['per_monades'][$row['product_monada_id']];
          if ($row_sintages['bom_disable']==0) $total_cost+=$bom_kostos;
        }
      } else if (isset($calc_res['base']['per_monades'][$row['product_monada_id']])) {
        $bom_kostos=$calc_res['base']['per_monades'][$row['product_monada_id']];
        if ($row_sintages['bom_disable']==0) $total_cost+=$bom_kostos;
      }
    }
    
    $temp.='<tr>'.
    '<th class="mytdcm" scope="row" nowrap="">'.$ss.'</th>'.
    '<td class="mytdcm" nowrap><a href="admin-production-bom-item.php?id='.$row_sintages['id_production_bom'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
    '<td class="mytdcml" >'.$row_sintages['bom_descr'].'</td>'.
    '<td class="mytdcm" nowrap>'.($bom_kostos==0 ? '' : myCurrencyFormat($bom_kostos)).'</td>'.
    '<td class="mytdcm" nowrap>'.myimg010r($row_sintages['bom_disable']).'</td>'.
    '</tr>';
  }
  if ($temp!='') {
    $temp=
    '<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
        '<tr>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> '.
          '<th class="table-dark" scope="col" style="text-align: left   !important;" width="100%">'.gks_lang('Περιγραφή').'</th> '.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κόστος').'</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ενεργή').'</th>'.
        '</tr>'.
      '</thead>'.
      '<tbody>'.
      $temp.
      '</tbody>'.
    '</table>';
  }
  $temp_yliko='';

  $sql_sintages="SELECT gks_production_bom.id_production_bom, 
  gks_production_bom.bom_descr, 
  gks_production_bom.bom_disable, 
  gks_production_bom.bom_kostos,bom_kostos_min,bom_kostos_max
  FROM gks_production_bom_product 
  LEFT JOIN gks_production_bom ON gks_production_bom_product.production_bom_id = gks_production_bom.id_production_bom
  WHERE gks_production_bom_product.pbom_product_id in (".$id.",".$variable_item['id_product'].")
  AND gks_production_bom.id_production_bom Is Not Null
  GROUP BY gks_production_bom.id_production_bom
  ORDER BY gks_production_bom.bom_descr;";
  $result_sintages = $db_link->query($sql_sintages);        
  if (!$result_sintages) {debug_mail(false,'error sql',$sql_sintages);die('sql error');}
  
  $ss=0;
  while ($row_sintages = $result_sintages->fetch_assoc()) {
    $ss++;
    $temp1='';
    if ($row_sintages['bom_kostos']!=0) $temp1= myCurrencyFormat($row_sintages['bom_kostos']);
    $temp2='';
    if ($row_sintages['bom_kostos_min']!=0 or $row_sintages['bom_kostos_max']!=0) {
      if (!($row_sintages['bom_kostos_min']==$row_sintages['bom_kostos'] and $row_sintages['bom_kostos_max']==$row_sintages['bom_kostos'])) {
        if ($row_sintages['bom_kostos_min']!=0) $temp2=myCurrencyFormat($row_sintages['bom_kostos_min']);
        if ($row_sintages['bom_kostos_max']!=0) $temp2.= ' &#8767; '.myCurrencyFormat($row_sintages['bom_kostos_max']);
        if ($temp1!='' and $temp2!='') $temp2='<br>'.$temp2;
      }
    }
    //echo $temp1.$temp2;
    
    $temp_yliko.='<tr>'.
    '<th class="mytdcm" scope="row" nowrap="">'.$ss.'</th>'.
    '<td class="mytdcm" nowrap><a href="admin-production-bom-item.php?id='.$row_sintages['id_production_bom'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
    '<td class="mytdcml" >'.$row_sintages['bom_descr'].'</td>'.
    '<td class="mytdcm" nowrap>'.$temp1.$temp2.'</td>'.
    '<td class="mytdcm" nowrap>'.myimg010r($row_sintages['bom_disable']).'</td>'.
    '</tr>';
  }
  if ($temp_yliko!='') {
    $temp_yliko=
    '<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
        '<tr>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> '.
          '<th class="table-dark" scope="col" style="text-align: left   !important;" width="100%">'.gks_lang('Περιγραφή').'</th> '.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κόστος').'</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ενεργή').'</th>'.
        '</tr>'.
      '</thead>'.
      '<tbody>'.
      $temp_yliko.
      '</tbody>'.
    '</table>';
  }
    
}     
?>
<div class="form-group row">
  <label for="variable_product_kostos_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κόστος');?>:</label>
  <div class="col-md-8">
    <div class="row">
      <div class="col-md-6">
        <input id="variable_product_kostos_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_kostos form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_kostos'])) echo myNumberFormatNo0($variable_item['product_kostos']);?>">
      </div>
      <div class="col-md-5">
        <small><?php echo gks_lang('Χωρίς ΦΠΑ','part4','fpa_base_descr');?></small>
      </div>
    </div>
    <?php if ($temp!='') {?>
    <div class="row" style="margin-top: 10px;">
      <div class="col-md-12 gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
        <button class="btn btn-primary btn-sm set_bom_kostos" data-val="<?php echo myNumberFormatNo0($total_cost);?>" data-to="variable_product_kostos_<?php echo $paa;?>"><?php echo gks_lang('Ορισμός από συνταγές');?></button>
      </div>
    </div>
    <?php } ?>
    
  </div>
</div>



<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;<?php if ($product_need_apostoli==0) echo 'display:none;';?>" class="div_product_need_apostoli"></div>
     
<div class="form-group row">
  <label for="variable_product_price_yperx_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή ΥπερΧονδρικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_yperx_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_price_yperx']);?>">
  </div>
  <label for="variable_product_price_yperx_include_vat_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_yperx_include_vat_<?php echo $paa;?>" value="1" <?php if ($variable_item['product_price_yperx_include_vat']!=0) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_include_vat switchery1_this">
  </div>
</div>
<div class="form-group row">
  <label for="variable_product_price_yperx_sale_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσφορά ΥπερΧονδρικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_yperx_sale_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_sale form-control form-control-sm myneedsave" value="<?php if ($variable_item['product_price_yperx_sale']!=0) echo myNumberFormatNo0($variable_item['product_price_yperx_sale']);?>">
  </div>
  <label for="variable_product_price_yperx_sale_dates_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_yperx_sale_dates_<?php echo $paa;?>" value="1" <?php if (isset($variable_item['product_price_yperx_sale_from']) or isset($variable_item['product_price_yperx_sale_to'])) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_sale_dates switchery1_this">
  </div>
</div>

<div class="variable_div_product_price_yperx_sale_dates form-group row" data-paa="<?php echo $paa;?>" style="<?php if (!(isset($variable_item['product_price_yperx_sale_from']) or isset($variable_item['product_price_yperx_sale_to']))) echo 'display:none;'?>">
  <label for="variable_product_price_yperx_sale_from_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_yperx_sale_from_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_sale_from form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_yperx_sale_from'])) echo  showDate(strtotime($variable_item['product_price_yperx_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
  <label for="variable_product_price_yperx_sale_to_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_yperx_sale_to_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_sale_to form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_yperx_sale_to'])) echo  showDate(strtotime($variable_item['product_price_yperx_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
</div>


<div class="form-group row">
  <label for="variable_product_price_yperx_sheets_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_yperx_sheets_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_sheets_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_yperx_sheets_formula']);?>">
  </div>
  
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[sheets] : <?php echo gks_lang('Σελίδες');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή υπερχονδρικής');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[sheets]*[itemprice]
    <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
      ></i></div>
</div>                 
<div class="form-group row">
  <label for="variable_product_price_yperx_quantity_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_yperx_quantity_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_yperx_quantity_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_yperx_quantity_formula']);?>">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή υπερχονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[quantity]*[itemprice]
    <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
      ></i></div>
</div>


<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
 
           
                   
<div class="form-group row">
  <label for="variable_product_price_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή Χονδρικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_price']);?>">
  </div>
  <label for="variable_product_price_include_vat_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_include_vat_<?php echo $paa;?>" value="1" <?php if ($variable_item['product_price_include_vat']!=0) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_include_vat switchery1_this">
  </div>
</div>
<div class="form-group row">
  <label for="variable_product_price_sale_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσφορά Χονδρικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_sale_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_sale form-control form-control-sm myneedsave" value="<?php if ($variable_item['product_price_sale']!=0) echo myNumberFormatNo0($variable_item['product_price_sale']);?>">
  </div>
  <label for="variable_product_price_sale_dates_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_sale_dates_<?php echo $paa;?>" value="1" <?php if (isset($variable_item['product_price_sale_from']) or isset($variable_item['product_price_sale_to'])) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_sale_dates switchery1_this">
  </div>
</div>
<div class="variable_div_product_price_sale_dates form-group row" data-paa="<?php echo $paa;?>" style="<?php if (!(isset($variable_item['product_price_sale_from']) or isset($variable_item['product_price_sale_to']))) echo 'display:none;'?>">
  <label for="variable_product_price_sale_from_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_sale_from_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_sale_from form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_sale_from'])) echo  showDate(strtotime($variable_item['product_price_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
  <label for="variable_product_price_sale_to_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_sale_to_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_sale_to form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_sale_to'])) echo  showDate(strtotime($variable_item['product_price_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
</div>


<div class="form-group row">
  <label for="variable_product_price_sheets_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_sheets_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_sheets_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_sheets_formula']);?>">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[sheets] : <?php echo gks_lang('Σελίδες');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[sheets]*[itemprice]
    <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
      ></i></div>
</div>                 
<div class="form-group row">
  <label for="variable_product_price_quantity_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_quantity_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_quantity_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_quantity_formula']);?>">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[quantity]*[itemprice]
    <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
      ></i></div>
</div>

<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>


<div class="form-group row">
  <label for="variable_product_price_retail_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή Λιανικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_retail_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_retail form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_price_retail']);?>">
  </div>
  <label for="variable_product_price_retail_include_vat_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_retail_include_vat_<?php echo $paa;?>" value="1" <?php if ($variable_item['product_price_retail_include_vat']!=0) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_retail_include_vat switchery1_this">
  </div>
</div>
<div class="form-group row">
  <label for="variable_product_price_retail_sale_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσφορά Λιανικής');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_retail_sale_<?php echo $paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_retail_sale form-control form-control-sm myneedsave" value="<?php if ($variable_item['product_price_retail_sale']!=0) echo myNumberFormatNo0($variable_item['product_price_retail_sale']);?>">
  </div>
  <label for="variable_product_price_retail_sale_dates_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
  <div class="col-md-2">
    <input type="checkbox" id="variable_product_price_retail_sale_dates_<?php echo $paa;?>" value="1" <?php if (isset($variable_item['product_price_retail_sale_from']) or isset($variable_item['product_price_retail_sale_to'])) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_retail_sale_dates switchery1_this">
  </div>
</div>

<div class="variable_div_product_price_retail_sale_dates form-group row" data-paa="<?php echo $paa;?>" style="<?php if (!(isset($variable_item['product_price_retail_sale_from']) or isset($variable_item['product_price_retail_sale_to']))) echo 'display:none;'?>">
  <label for="variable_product_price_retail_sale_from_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_retail_sale_from_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_retail_sale_from form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_retail_sale_from'])) echo  showDate(strtotime($variable_item['product_price_retail_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
  <label for="variable_product_price_retail_sale_to_<?php echo $paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
  <div class="col-md-4">
    <input id="variable_product_price_retail_sale_to_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_retail_sale_to form-control form-control-sm myneedsave" value="<?php if (isset($variable_item['product_price_retail_sale_to'])) echo  showDate(strtotime($variable_item['product_price_retail_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
  </div>
</div>


<div class="form-group row">
  <label for="variable_product_price_retail_sheets_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_retail_sheets_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_retail_sheets_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_retail_sheets_formula']);?>">
  </div>
  
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[sheets] : <?php echo gks_lang('Σελίδες');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή λιανικής');?>
    <br>[price] : <?php echo gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής');?>
    <br>echo gks_lang('π.χ.');?>
    <br>[price]*1.5
    <br>[sheets]*[itemprice]
    <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
      ></i></div>
</div>                 
<div class="form-group row">
  <label for="variable_product_price_retail_quantity_formula_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
  <div class="col-md-7">
    <input id="variable_product_price_retail_quantity_formula_<?php echo $paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_retail_quantity_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($variable_item['product_price_retail_quantity_formula']);?>">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή λιανικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
    <br>[price] : <?php echo gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής');?>
    <br>echo gks_lang('π.χ.');?>
    <br>[quantity]*[price]*0.9
    <br>[quantity]*[itemprice]
    <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
      ></i></div>
</div>

<?php 

foreach ($product_price_plist as $plist) {
  $plist_id='_'.$plist['id_pricelist'];
  if (isset($plist['products'][$variable_item['id_product']])) {
    $pplist=$plist['products'][$variable_item['id_product']];  
  } else {
    $pplist=array(
      'product_price_plist'=>0,
      'product_price_plist_sale'=>0,
      'product_price_plist_sale_from'=>'',
      'product_price_plist_sale_to'=>'',
      'product_price_plist_sheets_formula'=>'',
      'product_price_plist_quantity_formula'=>'',
      'product_price_plist_include_vat'=>0,
      'quantitycheck_price_plist'=>'',
    );;
  }
?>


<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

<div class="gks_variable_product_price_plist_item" data-id_pricelist="<?php echo $plist['id_pricelist'];?>">
  <div class="form-group row">
    <label for="variable_product_price_plist<?php echo $plist_id.'_'.$paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo $plist['pricelist_descr'];?>:</label>
    <div class="col-md-4">
      <input id="variable_product_price_plist<?php echo $plist_id.'_'.$paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_plist form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($pplist['product_price_plist']);?>">
    </div>
    <label for="variable_product_price_plist_include_vat<?php echo $plist_id.'_'.$paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
    <div class="col-md-2">
      <input type="checkbox" id="variable_product_price_plist_include_vat<?php echo $plist_id.'_'.$paa;?>" value="1" <?php if ($pplist['product_price_plist_include_vat']!=0) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_plist_include_vat switchery1_this">
    </div>
  </div>
  <div class="form-group row">
    <label for="variable_product_price_plist_sale<?php echo $plist_id.'_'.$paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσφορά').' '.$plist['pricelist_descr'];?>:</label>
    <div class="col-md-4">
      <input id="variable_product_price_plist_sale<?php echo $plist_id.'_'.$paa;?>" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" data-paa="<?php echo $paa;?>" class="variable_product_price_plist_sale form-control form-control-sm myneedsave" value="<?php if ($pplist['product_price_plist_sale']!=0) echo myNumberFormatNo0($pplist['product_price_plist_sale']);?>">
    </div>
    <label for="variable_product_price_plist_sale_dates<?php echo $plist_id.'_'.$paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
    <div class="col-md-2">
      <input type="checkbox" data-plist_id="<?php echo $plist_id;?>" id="variable_product_price_plist_sale_dates<?php echo $plist_id.'_'.$paa;?>" value="1" <?php if (!empty($pplist['product_price_plist_sale_from']) or !empty($pplist['product_price_plist_sale_to'])) echo ' checked '; ?> data-paa="<?php echo $paa;?>" class="variable_product_price_plist_sale_dates switchery1_this">
    </div>
  </div>
  
  <div class="variable_div_product_price_plist_sale_dates<?php echo $plist_id;?> form-group row" data-paa="<?php echo $paa;?>" style="<?php if (!(!empty($pplist['product_price_plist_sale_from']) or !empty($pplist['product_price_plist_sale_to']))) echo 'display:none;'?>">
    <label for="variable_product_price_plist_sale_from<?php echo $plist_id.'_'.$paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
    <div class="col-md-4">
      <input id="variable_product_price_plist_sale_from<?php echo $plist_id.'_'.$paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_plist_sale_from form-control form-control-sm myneedsave" value="<?php if (!empty($pplist['product_price_plist_sale_from'])) echo  showDate(strtotime($pplist['product_price_plist_sale_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
    </div>
    <label for="variable_product_price_plist_sale_to<?php echo $plist_id.'_'.$paa;?>" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
    <div class="col-md-4">
      <input id="variable_product_price_plist_sale_to<?php echo $plist_id.'_'.$paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_plist_sale_to form-control form-control-sm myneedsave" value="<?php if (!empty($pplist['product_price_plist_sale_to'])) echo  showDate(strtotime($pplist['product_price_plist_sale_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
    </div>
  </div>
  
  
  <div class="form-group row">
    <label for="variable_product_price_plist_sheets_formula<?php echo $plist_id.'_'.$paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
    <div class="col-md-7">
      <input id="variable_product_price_plist_sheets_formula<?php echo $plist_id.'_'.$paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_plist_sheets_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pplist['product_price_plist_sheets_formula']);?>">
    </div>
    
    <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
      title="[sheets] : <?php echo gks_lang('Σελίδες');?>
      <br>[itemprice] : <?php echo gks_lang('η τιμή').' '.$plist['pricelist_descr'];?>
      <br><?php echo gks_lang('π.χ.');?>
      <br>[sheets]*[itemprice]
      <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
        ></i></div>
  </div>                 
  <div class="form-group row">
    <label for="variable_product_price_plist_quantity_formula<?php echo $plist_id.'_'.$paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
    <div class="col-md-7">
      <input id="variable_product_price_plist_quantity_formula<?php echo $plist_id.'_'.$paa;?>" type="text" data-paa="<?php echo $paa;?>" class="variable_product_price_plist_quantity_formula form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pplist['product_price_plist_quantity_formula']);?>">
    </div>
    <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
      title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
      <br>[itemprice] : <?php echo str_replace('[1]',$plist['pricelist_descr'], gks_lang('η τιμή [1] ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου'));?>
      <br><?php echo gks_lang('π.χ.');?>
      <br>[quantity]*[itemprice]
      <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
        ></i></div>
  </div>
</div>

<?php }  ?>





<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>




<div class="form-group row div_product_need_apostoli" style="<?php if ($product_need_apostoli==0) echo 'display:none;';?>">
  <label for="variable_product_varos_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βάρος σε gr');?>:</label>
  <div class="col-md-8">
    <input id="variable_product_varos_<?php echo $paa;?>" type="number" data-paa="<?php echo $paa;?>" class="variable_product_varos form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_varos'],true);?>" min=0 step="0.01" style="max-width:150px">
  </div>
</div>
<div class="form-group row div_product_need_apostoli" style="<?php if ($product_need_apostoli==0) echo 'display:none;';?>">
  <label for="variable_product_ogos_x_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαστάσεις σε cm');?>:</label>
  <div class="col-md-8">
    <div class="row">
      <div class="col-md-4" style="padding-right:0px;">
        <label for="variable_product_ogos_x_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;padding-left:0px;"><?php echo gks_lang('Μήκος');?>:</label>
        <input id="variable_product_ogos_x_<?php echo $paa;?>" type="number" data-paa="<?php echo $paa;?>" class="variable_product_ogos_x form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_ogos_x'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
      </div>                        
      <div class="col-md-4" style="padding-left:0px;padding-right:0px;">
        <label for="variable_product_ogos_y_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Πλάτος');?>:</label>
        <input id="variable_product_ogos_y_<?php echo $paa;?>" type="number" data-paa="<?php echo $paa;?>" class="variable_product_ogos_y form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_ogos_y'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
      </div>
      <div class="col-md-4" style="padding-left:0px;">
        <label for="variable_product_ogos_z_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Ύψος');?>:</label>
        <input id="variable_product_ogos_z_<?php echo $paa;?>" type="number" data-paa="<?php echo $paa;?>" class="variable_product_ogos_z form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($variable_item['product_ogos_z'], true);?>" style="display: inline;max-width: 70px;" min=0 step="0.01">
      </div>
    </div>
  </div>                      
</div>

<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

<div class="form-group row">
  <label for="variable_product_fpa_base_id_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ');?>:</label>
  <div class="col-md-8">
    <select id="variable_product_fpa_base_id_<?php echo $paa;?>" data-paa="<?php echo $paa;?>" class="variable_product_fpa_base_id form-control form-control-sm myneedsave" style="max-width:200px">
    <option value="0"></option>
    <?php
    foreach ($product_fpa_base_id_array as $row_select) {
      echo '<option value="'.$row_select['id_fpa_base'].'" ';
      if ($row_select['id_fpa_base']==$variable_item['product_fpa_base_id']) echo ' selected ';
      echo '>'.gks_lang($row_select['fpa_base_descr'].'','part4','fpa_base_descr').'</option>';
    }?></select>
  </div>
</div>

<div class="gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>;height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

<div class="form-group row gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">
  <label for="variable_min_quantity_alert_<?php echo $paa;?>" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όριο αποθέματος');?>:</label>
  <div class="col-md-4">
    <input id="variable_min_quantity_alert_<?php echo $paa;?>" data-paa="<?php echo $paa;?>" type="number" class="variable_min_quantity_alert form-control form-control-sm myneedsave" value="<?php if ($variable_item['min_quantity_alert']!=0) echo $variable_item['min_quantity_alert'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>">
  </div>
</div>  

<?php if ($GKS_ORDERS_PRODUCTION) { ?>

<div class="gks_base_type0 gks_base_type1" style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>"></div>

<div class="form-group row gks_base_type0 gks_base_type1" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?>">
  <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνταγές');?>:</label>
  <div class="col-md-8">
    <div class="row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?>">
      <div class="col-md-12">
        <div style="font-size: 0.875rem;padding: 0.25rem;"><?php echo gks_lang('Ως παραγόμενο');?>:</div>
        <?php echo $temp;?>
      </div>
    </div>
    <div class="row gks_base_type1" style="<?php if (!($row['product_base_type']==1)) echo 'display:none;';?><?php if ($temp!='') echo 'margin-top: 10px;'?>">
      <div class="col-md-12">
        <a class="btn btn-primary btn-sm" href="admin-production-bom-item.php?id=-1&product_id=<?php echo $variable_item['id_product'];?>"><?php echo gks_lang('Προσθήκη συνταγής');?></a>
      </div>
    </div>
    <?php if (1==1 or $temp_yliko!='') { ?>
    <div class="row gks_base_type0 gks_base_type1 gks_base_type1_pt12" style="<?php if (!($row['product_base_type']==0 or $row['product_base_type']==1)) echo 'display:none;';?><?php if ($row['product_base_type']==1) echo 'padding-top: 12px;';?>">
      <div class="col-md-12">
        <div style="font-size: 0.875rem;padding: 0.25rem;"><?php echo gks_lang('Ως υλικό');?>:</div>
        <?php echo $temp_yliko;?>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
<?php } ?>
            
            </div>
          </div>
     
      
          
        
          
          
        <?php } ?>
          </div> 
          <div>
            <div class="alert alert-primary" role="alert" id="div_variables_count">
              <?php echo str_replace('[1]','<span id="span_variables_count">'.count($product_variables).'</span>',gks_lang('Έχετε [1] παραλλαγές'));?>
               
            </div>
            <div class="alert alert-warning" role="alert"  id="div_variables_warning" style="display:none;">
            </div>
            <div class="alert alert-danger" role="alert" id="div_variables_danger" style="display:none;">
            </div>
            
          </div>
          
      
      
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Σχόλιο');?></span>
        </div>
        <div class="card-body" <?php echo gks_card_body('note');?>>
          <div class="form-group row">
            <label for="internal_note" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερικό Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="internal_note" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;"><?php echo htmlspecialchars_gks($row['internal_note']);?></textarea>
            </div>
          </div>
          
        </div>
      </div>
      
    </div>

  </div>
</div>




<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_product'];?>" data-model="gks_eshop_products" data-backurl="admin-products.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-primary" id="submit_button_copy" onclick="window.location.href='admin-products-item.php?id=-1&copy=<?php echo $id;?>'"><?php echo gks_lang('Δημιουργία αντιγράφου');?></button>
      <?php } ?>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Σύνδεση με προϊόν eshop');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="eshoplink_add" style="margin-left: 10px;"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('eshops');?>>
          
          <?php
         
          $query = "SELECT gks_woo_product.*, gks_eshops.eshop_name, gks_eshops.eshop_url, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM (gks_woo_product 
          LEFT JOIN gks_eshops ON gks_woo_product.eshop_id = gks_eshops.id_eshop) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_woo_product.last_update_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_woo_product.product_id=".$id."
          ORDER BY gks_eshops.eshop_sortorder, gks_woo_product.id_woo_product;";



          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="eshoplink_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%">eshop</th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"  >url</th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap><?php echo gks_lang('ID eshop');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap><?php echo gks_lang('Γλώσσα');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap><span class="tooltipster" title="<?php echo gks_lang('Συγχρονισμός');?>"><?php echo gks_lang('Συγχ');?></span></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap><span class="tooltipster" title="<?php echo gks_lang('Τελευταία ενημέρωση πότε και από ποιον');?>"><?php echo gks_lang('Ενημε.');?></span></th> 
                     
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="eshoplink_tr_exist" data-id="<?php echo $row_list['id_woo_product'];?>">
              <th scope="row" nowrap class="mytdcm eshoplink_aa"><?php echo ($i);?></td>       
              <td nowrap class="mytdcm">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_eshoplink_delete_after|<?php echo $row_list['id_woo_product'];?>" data-id="<?php echo $row_list['id_woo_product'];?>" data-model="gks_woo_product">
              </td>
              <td class="mytdcm"><a href="admin-eshop-item.php?id=<?php echo $row_list['eshop_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td class="mytdcml"><?php echo $row_list['eshop_name'];?></td>  
              <td class="mytdcml"><a href="<?php echo $row_list['eshop_url'];?>" target="_blank"><?php echo $row_list['eshop_url'];?></a></td>  
              <td class="mytdcm" nowrap><a href="<?php echo $row_list['eshop_url'];?>/wp-admin/post.php?post=<?php echo $row_list['remote_product_id'];?>&action=edit" target="_blank"><?php echo $row_list['remote_product_id'];?></a></td>  
              <td class="mytdcm" nowrap><?php echo $row_list['remote_lang'];?></td>
              <td class="mytdcm" nowrap><i class="eshop_sync fas fa-sync-alt tooltipster" data-id_woo_product="<?php echo $row_list['id_woo_product'];?>" title="<?php echo gks_lang('Συγχρονισμός τώρα');?>"></i></td>
              <td class="mytdcm" nowrap ><span class="tooltipster" title="<?php echo gks_lang('Από').': '.$row_list['gks_nickname'];?>"><?php if (isset($row_list['last_update_date'])) echo showDate(strtotime($row_list['last_update_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></span></td>
            </tr>
          <?php } ?>


     
          </tbody>
          </table>      

          
        </div>
      </div>
      
      <?php 
      echo getObjectRels('gks_eshop_products',$id);
      echo getActivityObjectTable('gks_eshop_products',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshop_products','id'=>$id));
      echo $obj_fileslist['html'];
      
      
      ?>
      
    </div>
    
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κατηγορίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('catei');?>>        

          <?php
          //gks_eshop_products_categories.*,
          $query = "SELECT
gks_eshop_products_categories_products.*,
gks_eshop_products_categories.category_photo,
ccproducts.ccc,
ug2.product_category_descr AS gt2, 
ug3.product_category_descr AS gt3, 
ug4.product_category_descr AS gt4, 
ug5.product_category_descr AS gt5,
ug6.product_category_descr AS gt6,
ug7.product_category_descr AS gt7,
ug8.product_category_descr AS gt8,
ug9.product_category_descr AS gt9,
ug10.product_category_descr AS gt10,

ug2.id_product_category AS id2, 
ug3.id_product_category AS id3, 
ug4.id_product_category AS id4, 
ug5.id_product_category AS id5,
ug6.id_product_category AS id6,
ug7.id_product_category AS id7,
ug8.id_product_category AS id8,
ug9.id_product_category AS id9,
ug10.id_product_category AS id10,
CONCAT_WS('\\\\',
        ug10.product_category_descr,
        ug9.product_category_descr,
        ug8.product_category_descr,
        ug7.product_category_descr,
        ug6.product_category_descr,
        ug5.product_category_descr,
        ug4.product_category_descr,
        ug3.product_category_descr,
        ug2.product_category_descr,
        gks_eshop_products_categories.product_category_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.product_category_descr,
        ug9.product_category_descr,
        ug8.product_category_descr,
        ug7.product_category_descr,
        ug6.product_category_descr,
        ug5.product_category_descr,
        ug4.product_category_descr,
        ug3.product_category_descr,
        ug2.product_category_descr) as dirpath
FROM ((((((((((gks_eshop_products_categories_products
LEFT JOIN gks_eshop_products_categories ON gks_eshop_products_categories_products.product_category_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN (
SELECT product_category_id, Count(product_id) AS ccc
FROM gks_eshop_products_categories_products
GROUP BY product_category_id
) AS ccproducts ON gks_eshop_products_categories.id_product_category = ccproducts.product_category_id)
LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
WHERE gks_eshop_products_categories_products.product_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="categories_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Κατηγορία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="categories_tr_exist" data-id="<?php echo $row_list['id_eshop_products_categories_products'];?>">
              <th scope="row" nowrap align="right" class="categories_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_categories_delete_after|<?php echo $row_list['id_eshop_products_categories_products'];?>" data-id="<?php echo $row_list['id_eshop_products_categories_products'];?>" data-model="gks_eshop_products_categories_products">            
              </td>
              <td nowrap align="center"><a href="admin-product-categories-item.php?id=<?php echo $row_list['product_category_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo getCategoryPhoto($row_list['product_category_id'],$row_list['category_photo'],32);?></td>  
              <td nowrap><?php echo $row_list['fullpath'];?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="cateidos"    id="cateidos"   class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="cateidos_id" id="cateidos_id">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_cateidos"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>    


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μάρκα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('brands');?>>        

          <?php
          //gks_eshop_products_brands.*,
          $query = "SELECT
gks_eshop_products_brands_products.*,
gks_eshop_products_brands.brand_photo,
ccproducts.ccc,
ug2.product_brand_descr AS gt2, 
ug3.product_brand_descr AS gt3, 
ug4.product_brand_descr AS gt4, 
ug5.product_brand_descr AS gt5,
ug6.product_brand_descr AS gt6,
ug7.product_brand_descr AS gt7,
ug8.product_brand_descr AS gt8,
ug9.product_brand_descr AS gt9,
ug10.product_brand_descr AS gt10,

ug2.id_product_brand AS id2, 
ug3.id_product_brand AS id3, 
ug4.id_product_brand AS id4, 
ug5.id_product_brand AS id5,
ug6.id_product_brand AS id6,
ug7.id_product_brand AS id7,
ug8.id_product_brand AS id8,
ug9.id_product_brand AS id9,
ug10.id_product_brand AS id10,
CONCAT_WS('\\\\',
        ug10.product_brand_descr,
        ug9.product_brand_descr,
        ug8.product_brand_descr,
        ug7.product_brand_descr,
        ug6.product_brand_descr,
        ug5.product_brand_descr,
        ug4.product_brand_descr,
        ug3.product_brand_descr,
        ug2.product_brand_descr,
        gks_eshop_products_brands.product_brand_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.product_brand_descr,
        ug9.product_brand_descr,
        ug8.product_brand_descr,
        ug7.product_brand_descr,
        ug6.product_brand_descr,
        ug5.product_brand_descr,
        ug4.product_brand_descr,
        ug3.product_brand_descr,
        ug2.product_brand_descr) as dirpath
FROM ((((((((((gks_eshop_products_brands_products
LEFT JOIN gks_eshop_products_brands ON gks_eshop_products_brands_products.product_brand_id = gks_eshop_products_brands.id_product_brand)
LEFT JOIN (
SELECT product_brand_id, Count(product_id) AS ccc
FROM gks_eshop_products_brands_products
GROUP BY product_brand_id
) AS ccproducts ON gks_eshop_products_brands.id_product_brand = ccproducts.product_brand_id)
LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
WHERE gks_eshop_products_brands_products.product_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="brands_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Brand');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="brands_tr_exist" data-id="<?php echo $row_list['id_eshop_products_brands_products'];?>">
              <th scope="row" nowrap align="right" class="brands_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_brands_delete_after|<?php echo $row_list['id_eshop_products_brands_products'];?>" data-id="<?php echo $row_list['id_eshop_products_brands_products'];?>" data-model="gks_eshop_products_brands_products">            
              </td>
              <td nowrap align="center"><a href="admin-product-brands-item.php?id=<?php echo $row_list['product_brand_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              
              <td nowrap><?php echo getBrandPhoto($row_list['product_brand_id'],$row_list['brand_photo'],32);?></td>  
              <td nowrap><?php echo $row_list['fullpath'];?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="brand_eidos"    id="brand_eidos"   class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="brand_eidos_id" id="brand_eidos_id">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_brand_eidos"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>


      <?php if ($GKS_ORDERS_PRODUCTION) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center;">
          <?php echo gks_lang('Εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erga');?>>        

          <div class="form-group row">
            <label for="use_only_mine_ergasies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρήση μόνο των παρακάτω εργασιών');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="use_only_mine_ergasies"  id="use_only_mine_ergasies" value="1" <?php if ($row['use_only_mine_ergasies']!=0) echo ' checked '; ?> class="switchery1_this">
              <small><i><?php echo gks_lang('Θα αγνοηθούν οι εργασίες από την κατηγορία');?></i></small>
            </div>
          </div>
          
          <?php
          $query = "SELECT gks_production_ergasies_eidos.*, gks_production_ergasies.production_ergasia_descr
          FROM gks_production_ergasies_eidos
          LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidos.production_ergasia_id = gks_production_ergasies.id_production_ergasia
          WHERE gks_production_ergasies_eidos.eidos_id=".$id."
          ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="ergasies_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Εργασία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="ergasies_tr_exist" data-id="<?php echo $row_list['id_production_ergasies_eidos'];?>">
              <th scope="row" nowrap align="right" class="ergasies_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_ergasies_delete_after|<?php echo $row_list['id_production_ergasies_eidos'];?>" data-id="<?php echo $row_list['id_production_ergasies_eidos'];?>" data-model="gks_production_ergasies_eidos">
              </td>
              <td nowrap align="center"><a href="admin-production-ergasies-item.php?id=<?php echo $row_list['production_ergasia_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo $row_list['production_ergasia_descr'];?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="ergasia"    id="ergasia"   class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="ergasia_id" id="ergasia_id">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_ergasia"><?php echo gks_lang('Προσθήκη');?></button>
                
                
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>
      
      <?php
      
      
      function gks_production_get_product_map($id) {
        global $db_link;
        
      
      
        $sql="SELECT id_product, product_parent_id, product_class, product_code, product_descr, use_only_mine_ergasies
        FROM gks_eshop_products
        WHERE id_product=".$id;
        $result = $db_link->query($sql); 
        if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}

        $orders_products_array=array();
        while ($row = $result->fetch_assoc()) {
          $orders_products_array[$row['id_product']] = $row;
          
        }

        foreach ($orders_products_array as &$product) {
          
          if ($product['use_only_mine_ergasies']==0) {
            
            $sql="SELECT product_category_id
            FROM gks_eshop_products_categories_products
            WHERE product_id=".$product['id_product'];
            if ($product['product_parent_id']>0) $sql.=" or product_id=".$product['product_parent_id'];
            
            
            $result = $db_link->query($sql);        
            if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
            $product_category_id=array();
            while ($row = $result->fetch_assoc()) {
              $product_category_id[] = $row['product_category_id'];
            }
      
            $gids=array();
            if (count($product_category_id)>0) {
              $sql="SELECT 
              ug1.id_product_category as gid1, 
              ug2.id_product_category as gid2, 
              ug3.id_product_category as gid3,
              ug4.id_product_category as gid4,
              ug5.id_product_category as gid5,
              ug6.id_product_category as gid6,
              ug7.id_product_category as gid7,
              ug8.id_product_category as gid8,
              ug9.id_product_category as gid9,
              ug10.id_product_category as gid10
              FROM ((((((((gks_eshop_products_categories AS ug1 
              LEFT JOIN gks_eshop_products_categories AS ug2 ON ug1.product_category_parent_id = ug2.id_product_category) 
              LEFT JOIN gks_eshop_products_categories AS ug3 ON ug2.product_category_parent_id = ug3.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug4 ON ug3.product_category_parent_id = ug4.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug5 ON ug4.product_category_parent_id = ug5.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug6 ON ug5.product_category_parent_id = ug6.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug7 ON ug6.product_category_parent_id = ug7.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug8 ON ug7.product_category_parent_id = ug8.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug9 ON ug8.product_category_parent_id = ug9.id_product_category)
              LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
              WHERE ug1.id_product_category in (".implode(',',$product_category_id).")";
              
              $result = $db_link->query($sql);        
              if (!$result) gks_production_order_ergasies_error('sql error',$sql);
              while ($row = $result->fetch_assoc()) {
                if (!empty($row['gid1']))  if (in_array(intval($row['gid1']), $gids)==false) $gids[] = intval($row['gid1']);
                if (!empty($row['gid2']))  if (in_array(intval($row['gid2']), $gids)==false) $gids[] = intval($row['gid2']);
                if (!empty($row['gid3']))  if (in_array(intval($row['gid3']), $gids)==false) $gids[] = intval($row['gid3']);
                if (!empty($row['gid4']))  if (in_array(intval($row['gid4']), $gids)==false) $gids[] = intval($row['gid4']);
                if (!empty($row['gid5']))  if (in_array(intval($row['gid5']), $gids)==false) $gids[] = intval($row['gid5']);
                if (!empty($row['gid6']))  if (in_array(intval($row['gid6']), $gids)==false) $gids[] = intval($row['gid6']);
                if (!empty($row['gid7']))  if (in_array(intval($row['gid7']), $gids)==false) $gids[] = intval($row['gid7']);
                if (!empty($row['gid8']))  if (in_array(intval($row['gid8']), $gids)==false) $gids[] = intval($row['gid8']);
                if (!empty($row['gid9']))  if (in_array(intval($row['gid9']), $gids)==false) $gids[] = intval($row['gid9']);
                if (!empty($row['gid10'])) if (in_array(intval($row['gid10']),$gids)==false) $gids[] = intval($row['gid10']);
              }
            }
      
            if (count($gids)>0) {     
              $sql="SELECT gks_production_ergasies.id_production_ergasia, gks_production_ergasies.production_ergasia_descr
              FROM gks_production_ergasies_eidoscat 
              LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidoscat.production_ergasia_id = gks_production_ergasies.id_production_ergasia
              WHERE gks_production_ergasies.id_production_ergasia Is Not Null 
              AND gks_production_ergasies_eidoscat.cateidos_id In (".implode(",",$gids).")
              ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.id_production_ergasia";
      
              $result = $db_link->query($sql);        
              if (!$result) gks_production_order_ergasies_error('sql error',$sql);
              while ($row = $result->fetch_assoc()) {
                $row['id_production_line']=0;
                $row['pl_state']='';
                $product['ergasies'][$row['id_production_ergasia']] = $row;
              }
            }
          }
          
          
          
          $sql="SELECT gks_production_ergasies.id_production_ergasia, gks_production_ergasies.production_ergasia_descr
          FROM gks_production_ergasies_eidos 
          LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidos.production_ergasia_id = gks_production_ergasies.id_production_ergasia
          WHERE gks_production_ergasies.id_production_ergasia Is Not Null
          and (gks_production_ergasies_eidos.eidos_id=".$product['id_product'];
          if ($product['product_parent_id']>0) $sql.=" or gks_production_ergasies_eidos.eidos_id=".$product['product_parent_id'];
          $sql.=")";
          
          
          $result = $db_link->query($sql);        
          if (!$result) gks_production_order_ergasies_error('sql error',$sql);
          while ($row = $result->fetch_assoc()) {
            if (isset($product['ergasies'][$row['id_production_ergasia']])==false) {
              $row['id_production_line']=0;
              $row['pl_state']='';
              $product['ergasies'][$row['id_production_ergasia']] = $row;
            }
          }
        } 
        unset($product);        
        
        $out='';
        if (isset($orders_products_array[$id]['ergasies'])) {
          foreach ($orders_products_array[$id]['ergasies'] as $value) {
            $out.=$value['production_ergasia_descr'].'<br>';
          }
        }
        
        return $out;
        
      }
      $out_temp= gks_production_get_product_map($id);
      if ($out_temp!='') {
      ?>      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center;">
          <?php echo gks_lang('Όλες οι εργασίες του είδους');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erga_map');?>>        
          <div style="font-size: 0.8rem;">
            
            <?php echo $out_temp;?>
          </div>
        </div>
      </div>      
      <?php } ?>
      <?php } ?>    
      
          
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>

          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_product']>0) echo $row['id_product'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
         
        </div>
        

      </div>

    </div>
      
  </div>
</div>

          
<div id="dialog_eshoplink" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Σύνδεση προϊόντος eshop');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <label for="dialog_eshoplink_eshop" class="col-md-12 col-form-label form-control-sm text-sm-center"><?php echo gks_lang('eshop');?>:</label>
      <div class="col-md-12 text-sm-center">
        <select id="dialog_eshoplink_eshop" class="form-control form-control-sm">
          <option value="0"></option>
          <?php
          $query = "SELECT id_eshop, eshop_name FROM gks_eshops ORDER BY eshop_sortorder;";
          $result_list = $db_link->query($query);
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          while ($row_list = $result_list->fetch_assoc()) {
            echo '<option value="'.$row_list['id_eshop'].'">'.$row_list['eshop_name'].'</option>';
          }?>
        </select>
      </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <label for="dialog_eshoplink_list" class="col-md-12 col-form-label form-control-sm text-sm-center"><?php echo gks_lang('Προϊόν');?>:</label>
      <div class="col-md-12 text-sm-center">
        <input type="text" class="form-control form-control-sm" id="dialog_eshoplink_search" placeholder="<?php echo gks_lang('Αναζήτηση');?> ..." style="margin-bottom: 10px;">
      </div>

      <div class="col-md-12 text-sm-center">
        <select id="dialog_eshoplink_list" class="form-control form-control-sm" size="10">
          <option value="0"></option>
        </select>
      </div>
    </div>

        
  </div>
</div>

<div id="dialog_variable_sindiasmoi" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Συνδυασμοί παραλλαγών');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <div style="text-align:center;width: 100%;">
      <button type="button" class="btn btn-sm btn-primary" id="dialog_variable_sindiasmoi_select_all" style="margin-right: 10px;"><?php echo gks_lang('Επιλογή όλων');?></button>
      <button type="button" class="btn btn-sm btn-primary" id="dialog_variable_sindiasmoi_select_none"><?php echo gks_lang('Αποεπιλογή όλων');?></button>
      </div>
    </div>
    <div class="row" style="margin-bottom: 10px;" id="dialog_variable_sindiasmoi_div_table">
      
    </div>
  </div>
</div>

<div id="dialog_variable_photo" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Επιλογή φωτογραφίας για την παραλλαγή από τις φωτογραφίες του είδους');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;" >
      <div id="dialog_variable_photo_list">
        
      </div>
    </div>
  </div>
</div>

<div id="dialog_variable_prices" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ορίστε τις παραμέτρους που θέλετε να ορίσετε στις παραλλαγές');?>.</div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;" id="dialog_variable_prices_mtype"></div>
      <div style="text-align:center;width: 100%;"><?php echo gks_lang('Ενεργοποιήστε τα πεδία που θέλετε να ενημερώσετε στις παραλλαγές');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;" >
      <div class="col-md-12 text-sm-center">


<div class="form-group row">
  <label for="variable_product_price_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή');?>:</label>
  <div class="col-md-4">
    <div style="text-align:left;" >
      <input type="checkbox" value="1" id="enable_variable_product_price_dialog">
      <input id="variable_product_price_dialog" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm" style="width: calc(100% - 30px);display: inline-block;">
    </div>
    <div style="text-align:left;" id="div_variable_product_price_dialog_type">
      <span style="white-space: nowrap;"><input type="radio" value="1" name="variable_product_price_dialog_type" id="variable_product_price_dialog_type1"><label for="variable_product_price_dialog_type1" class="gkslabelprice"> <?php echo gks_lang('Τιμή');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="2" name="variable_product_price_dialog_type" id="variable_product_price_dialog_type2"><label for="variable_product_price_dialog_type2" class="gkslabelprice"> <?php echo gks_lang('Αύξηση');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="3" name="variable_product_price_dialog_type" id="variable_product_price_dialog_type3"><label for="variable_product_price_dialog_type3" class="gkslabelprice"> <?php echo gks_lang('Μείωση');?></label></span>
    </div>
    <div style="text-align:left;" id="div_variable_product_price_dialog_type_poso">
      <span style="white-space: nowrap;"><input type="radio" value="1" name="variable_product_price_dialog_type_poso" id="variable_product_price_dialog_type_poso1"><label for="variable_product_price_dialog_type_poso1"> <?php echo gks_lang('Ποσό');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="2" name="variable_product_price_dialog_type_poso" id="variable_product_price_dialog_type_poso2"><label for="variable_product_price_dialog_type_poso2"> <?php echo gks_lang('Ποσοστό');?></label></span>
    </div>

  </div>
  <label for="variable_product_price_include_vat_dialog" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιέχει ΦΠΑ');?>:</label>
  <div class="col-md-2" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_include_vat_dialog">
    <input type="checkbox" id="variable_product_price_include_vat_dialog" value="1">
  </div>
</div>
<div class="form-group row">
  <label for="variable_product_price_sale_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσφορά');?>:</label>
  <div class="col-md-4">
    <div style="text-align:left;" >
      <input type="checkbox" value="1" id="enable_variable_product_price_sale_dialog">
      <input id="variable_product_price_sale_dialog" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>" class="form-control form-control-sm" style="width: calc(100% - 30px);display: inline-block;">
    </div>
    <div style="text-align:left;" id="div_variable_product_price_sale_dialog_type">
      <span style="white-space: nowrap;"><input type="radio" value="1" name="variable_product_price_sale_dialog_type" id="variable_product_price_sale_dialog_type1"><label for="variable_product_price_sale_dialog_type1" class="gkslabelprice"> <?php echo gks_lang('Τιμή');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="2" name="variable_product_price_sale_dialog_type" id="variable_product_price_sale_dialog_type2"><label for="variable_product_price_sale_dialog_type2" class="gkslabelprice"> <?php echo gks_lang('Αύξηση');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="3" name="variable_product_price_sale_dialog_type" id="variable_product_price_sale_dialog_type3"><label for="variable_product_price_sale_dialog_type3" class="gkslabelprice"> <?php echo gks_lang('Μείωση');?></label></span>
    </div>
    <div style="text-align:left;" id="div_variable_product_price_sale_dialog_type_poso">
      <span style="white-space: nowrap;"><input type="radio" value="1" name="variable_product_price_sale_dialog_type_poso" id="variable_product_price_sale_dialog_type_poso1"><label for="variable_product_price_sale_dialog_type_poso1"> <?php echo gks_lang('Ποσό');?></label></span>
      <span style="white-space: nowrap;"><input type="radio" value="2" name="variable_product_price_sale_dialog_type_poso" id="variable_product_price_sale_dialog_type_poso2"><label for="variable_product_price_sale_dialog_type_poso2"> <?php echo gks_lang('Ποσοστό');?></label></span>
    </div>
  </div>

  <label for="variable_product_price_sale_dates_dialog" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνίες');?>:</label>
  <div class="col-md-2" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_sale_dates_dialog">
    <input type="checkbox" id="variable_product_price_sale_dates_dialog" value="1">
  </div>
</div>
<div class="form-group row" style="" id="variable_div_product_price_sale_dates_dialog">
  <label for="variable_product_price_sale_from_dialog" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
  <div class="col-md-4" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_sale_from_dialog">
    <input id="variable_product_price_sale_from_dialog" type="text" class="form-control form-control-sm autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px;display: inline-block;">
  </div>
  <label for="variable_product_price_sale_to_dialog" class="col-md-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
  <div class="col-md-4" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_sale_to_dialog">
    <input id="variable_product_price_sale_to_dialog" type="text" class="form-control form-control-sm autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px;display: inline-block;">
  </div>
</div>


<div class="form-group row">
  <label for="variable_product_price_sheets_formula_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου');?>:</label>
  <div class="col-md-7" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_sheets_formula_dialog">
    <input id="variable_product_price_sheets_formula_dialog" type="text" class="form-control form-control-sm" style="width: calc(100% - 30px);display: inline-block;">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[sheets] : <?php echo gks_lang('Σελίδες');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[sheets]*[itemprice]
    <br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"
      ></i></div>
</div>                 
<div class="form-group row">
  <label for="variable_product_price_quantity_formula_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος υπολογισμού συνόλου');?>:</label>
  <div class="col-md-7" style="text-align:left;">
    <input type="checkbox" value="1" id="enable_variable_product_price_quantity_formula_dialog">
    <input id="variable_product_price_quantity_formula_dialog" type="text" class="form-control form-control-sm" style="width: calc(100% - 30px);display: inline-block;">
  </div>
  <div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" 
    title="[quantity] : <?php echo gks_lang('Ποσότητα');?>
    <br>[itemprice] : <?php echo gks_lang('η τιμή χονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου');?>
    <br><?php echo gks_lang('π.χ.');?>
    <br>[quantity]*[itemprice]
    <br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"
      ></i></div>
</div>

       
      </div>
    </div>
  </div>
</div>


<div id="dialog_variable_apostoli" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ορίστε τις παραμέτρους που θέλετε να ορίσετε στις παραλλαγές');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <div style="text-align:center;width: 100%;"><?php echo gks_lang('Ενεργοποιήστε τα πεδία που θέλετε να ενημερώσετε στις παραλλαγές');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;" >
      <div class="col-md-12 text-sm-center">
        
            <div class="form-group row" style="">
              <label for="product_varos_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βάρος σε gr');?>:</label>
              <div class="col-md-8">
                <input type="checkbox" value="1" id="enable_product_varos_dialog">
                <input id="product_varos_dialog" type="number" class="form-control form-control-sm" value="" min=0 step="0.01" style="display: inline-block;max-width:150px;" disabled>
              </div>
            </div>
            <div class="form-group row" style="">
              <label for="product_ogos_x_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαστάσεις σε cm');?>:</label>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-right:0px;">
                    <label for="product_ogos_x_dialog" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;padding-left:0px;"><?php echo gks_lang('Μήκος');?>:</label>
                    <input type="checkbox" value="1" id="enable_product_ogos_x_dialog">
                    <input id="product_ogos_x_dialog" type="number" class="form-control form-control-sm" value="" style="display: inline-block;max-width: 70px;" min=0 step="0.01" disabled>
                  </div>                        
                  <div class="col-md-4" style="padding-left:0px;padding-right:0px;">
                    <label for="product_ogos_y_dialog" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Πλάτος');?>:</label>
                    <input type="checkbox" value="1" id="enable_product_ogos_y_dialog">
                    <input id="product_ogos_y_dialog" type="number" class="form-control form-control-sm" value="" style="display: inline-block;max-width: 70px;" min=0 step="0.01" disabled>
                  </div>
                  <div class="col-md-4" style="padding-left:0px;">
                    <label for="product_ogos_z_dialog" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;"><?php echo gks_lang('Ύψος');?>:</label>
                    <input type="checkbox" value="1" id="enable_product_ogos_z_dialog">
                    <input id="product_ogos_z_dialog" type="number" class="form-control form-control-sm" value="" style="display: inline-block;max-width: 70px;" min=0 step="0.01" disabled>
                  </div>
                </div>
              </div>                      
            </div>


      </div>
    </div>
  </div>
</div>

<div id="dialog_variable_fpa" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ορίστε την παράμετρο που θέλετε να ορίσετε στις παραλλαγές');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;" >
      <div class="col-md-12 text-sm-center">

        <div class="form-group row">
          <label for="product_fpa_base_id_dialog" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ');?>:</label>
          <div class="col-md-8">
            <select id="product_fpa_base_id_dialog"  class="form-control form-control-sm" style="max-width:200px">
            <option value="0"></option>
            <?php
            foreach ($product_fpa_base_id_array as $row_select) {
              echo '<option value="'.$row_select['id_fpa_base'].'" ';
              echo '>'.gks_lang($row_select['fpa_base_descr'].'','part4','fpa_base_descr').'</option>';
            }?></select>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>


<div id="dialog_variable_list" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Λίστα παραλλαγών');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <div>
        <span style="font-weight:bold;"><?php echo gks_lang('Στήλες');?>: </span> 
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_0" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_0"># <?php echo gks_lang('(A/A)');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_1" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_1"><?php echo gks_lang('Παραλλαγή');?></label></span>                      
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_2" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_2"><?php echo gks_lang('Φωτό');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_3" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_3"><?php echo gks_lang('Κωδικός');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_4" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_4"><?php echo gks_lang('Περιγραφή');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_5" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_5"><?php echo gks_lang('SKU');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_6" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_6"><?php echo gks_lang('GTIN');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_7" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_7"><?php echo gks_lang('UPC');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_8" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_8"><?php echo gks_lang('EAN');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_9" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_9"><?php echo gks_lang('ISBN');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_10" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_10"><?php echo gks_lang('Taric No');?></label></span>

        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_27" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_27"><?php echo gks_lang('Κόστος');?></label></span>

        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_32" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_32"><?php echo gks_lang('Τιμή ΥπερΧονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_33" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_33"><?php echo gks_lang('Περιέχει ΦΠΑ');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_34" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_34"><?php echo gks_lang('Προσφορά ΥπερΧονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_35" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_35"><?php echo gks_lang('Ημερομηνίες Προσφοράς ΥπερΧονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_36" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_36"><?php echo gks_lang('Από');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_37" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_37"><?php echo gks_lang('Έως');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_38" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_38"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου ΥπερΧονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_39" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_39"><?php echo gks_lang('Τύπος υπολογισμού συνόλου ΥπερΧονδρικής');?></label></span>
        
        
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_11" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_11"><?php echo gks_lang('Τιμή Χονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_12" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_12"><?php echo gks_lang('Περιέχει ΦΠΑ');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_13" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_13"><?php echo gks_lang('Προσφορά Χονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_14" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_14"><?php echo gks_lang('Ημερομηνίες Προσφοράς Χονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_15" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_15"><?php echo gks_lang('Από');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_16" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_16"><?php echo gks_lang('Έως');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_17" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_17"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου Χονδρικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_18" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_18"><?php echo gks_lang('Τύπος υπολογισμού συνόλου Χονδρικής');?></label></span>
        
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_19" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_19"><?php echo gks_lang('Τιμή Λιανικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_20" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_20"><?php echo gks_lang('Περιέχει ΦΠΑ');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_21" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_21"><?php echo gks_lang('Προσφορά Λιανικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_22" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_22"><?php echo gks_lang('Ημερομηνίες Προσφοράς Λιανικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_23" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_23"><?php echo gks_lang('Από');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_24" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_24"><?php echo gks_lang('Έως');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_25" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_25"><?php echo gks_lang('Τύπος υπολογισμού τεμαχίου Λιανικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_26" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_26"><?php echo gks_lang('Τύπος υπολογισμού συνόλου Λιανικής');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_28" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_28"><?php echo gks_lang('Βάρος σε gr');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_29" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_29"><?php echo gks_lang('Διαστάσεις σε cm');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_30" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_30"><?php echo gks_lang('ΦΠΑ');?></label></span>
        <span class="def_column_show_span"><input type="checkbox" id="def_column_show_31" class="def_column_show_check"><label class="def_column_show_label" for="def_column_show_31"><?php echo gks_lang('Όριο αποθέματος');?></label></span>
        
      </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <div id="dialog_variable_list_table"></div>
    </div>
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_dialog_object_rel_curr='gks_eshop_products';
var from_php_activity_model='gks_eshop_products';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

  
var from_php_id=<?php echo $id;?>;



var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;

var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

var from_php_product_class='<?php echo $product_class;?>';
var from_php_get_variable_item=<?php echo $get_variable_item;?>;


 

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products','delete',$id);?>;

var def_column_show=[];
<?php
$sql="select myvalue from gks_settings_users where user_id=".$my_wp_user_id." and myobject='products_item' and mysubobject='def_column_show'";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);} else {
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    echo "def_column_show=JSON.parse($.base64.decode('".base64_encode(json_encode(unserialize($row['myvalue'])))."'));";
  }
}?>

var def_column_width=[];
<?php
$sql="select myvalue from gks_settings_users where user_id=".$my_wp_user_id." and myobject='products_item' and mysubobject='def_column_width'";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);} else {
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    echo "def_column_width=JSON.parse($.base64.decode('".base64_encode(json_encode(unserialize($row['myvalue'])))."'));";
  }
}?>

from_php_GKS_LANG_DATA_ARRAY = JSON.parse($.base64.decode('<?php echo base64_encode(json_encode( $GKS_LANG_DATA_ARRAY));?>'));
   
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  
});

</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>

<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-products-item.js"></script>


<script src="<?php echo gks_cache_idiotites_js_get_url();?>"></script>
<script src="js/admin-products-item.js?v=<?php echo $gks_cache_version;?>"></script>




<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


  