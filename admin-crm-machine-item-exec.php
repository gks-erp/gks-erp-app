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
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Συσκευής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_machine',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  

  
  $sql_row ="SELECT gks_crm_machine.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  
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
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
  table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
  gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
  gks_users.order_sxolio,gks_users.pelati_sxolio,
  gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
  gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
  gks_users.ma_country_id,gks_country.country_name,
  gks_users.ma_nomos_id,gks_nomoi.nomos_descr,
  
  gks_users_extra_address.ea_name
  FROM (((((((((((gks_crm_machine
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_crm_machine.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_crm_machine.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_products ON gks_crm_machine.crm_machine_product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
  LEFT JOIN gks_users_extra_address ON gks_crm_machine.users_extra_address_id = gks_users_extra_address.id_users_extra_address
  

  
  where gks_crm_machine.id_crm_machine = ".$id;
  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row_old = $result->fetch_assoc();

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_crm_machine',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
  
}


$crm_machine_name=''; if (isset($_POST['crm_machine_name'])) $crm_machine_name=trim_gks(base64_decode($_POST['crm_machine_name']));
$crm_machine_descr=''; if (isset($_POST['crm_machine_descr'])) $crm_machine_descr=trim_gks(base64_decode($_POST['crm_machine_descr']));
$crm_machine_serial_number=''; if (isset($_POST['crm_machine_serial_number'])) $crm_machine_serial_number=trim_gks(base64_decode($_POST['crm_machine_serial_number']));
$crm_machine_product_id=0; if (isset($_POST['crm_machine_product_id'])) $crm_machine_product_id=intval($_POST['crm_machine_product_id']);
$crm_machine_brand_id=0; if (isset($_POST['crm_machine_brand_id'])) $crm_machine_brand_id=intval($_POST['crm_machine_brand_id']);
$crm_machine_user_id=0; if (isset($_POST['crm_machine_user_id'])) $crm_machine_user_id=intval($_POST['crm_machine_user_id']);
$users_extra_address_id=0; if (isset($_POST['users_extra_address_id'])) $users_extra_address_id=intval($_POST['users_extra_address_id']);


if ($crm_machine_name=='') {debug_mail(false,'emptyl',           gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_crm_machine');

$sql_custom_data="select * from gks_customt_gks_crm_machine where crm_machine_id=".$id;
$result_custom_data = $db_link->query($sql_custom_data);  
if (!$result_custom_data) {
  debug_mail(false,'error sql',$sql_custom_data);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  
  
$row_custom_old=[];
if ($result_custom_data->num_rows>=1) {
  $row_custom_old = $result_custom_data->fetch_assoc();
}

//echo '<pre>';print_r($gks_custom_save_prepare);die();

$redirect='';
if ($id==-1) {
  $sql="insert into gks_crm_machine (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-crm-machine-item.php?id='.$id); 
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_crm_machine_log (crm_machine_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
    
}

$sql="update gks_crm_machine set 
crm_machine_name='".$db_link->escape_string($crm_machine_name)."',
crm_machine_descr='".$db_link->escape_string($crm_machine_descr)."',
crm_machine_serial_number='".$db_link->escape_string($crm_machine_serial_number)."',
crm_machine_product_id=".$crm_machine_product_id.",
crm_machine_brand_id=".$crm_machine_brand_id.",
crm_machine_user_id=".$crm_machine_user_id.",
users_extra_address_id=".$users_extra_address_id.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_crm_machine = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


if ($is_new_rec == false) {

  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_new = $result->fetch_assoc();
  
$sql_brand_template="SELECT gks_eshop_products_brands.*, ccproducts.ccc,
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
FROM (((((((((gks_eshop_products_brands
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
WHERE gks_eshop_products_brands.id_product_brand=[[%1]]
ORDER BY fullpath";  



  $crm_machine_brand_path_old='';
  $crm_machine_brand_id_old=intval($row_old['crm_machine_brand_id']);
  if ($crm_machine_brand_id_old>0) {
    $sql_brand=str_replace('[[%1]]', $crm_machine_brand_id_old, $sql_brand_template);
    $result_brand = $db_link->query($sql_brand);        
    if (!$result_brand) debug_mail(false,'error sql',$sql_brand);
    if (!$result_brand) die('sql error');
    if ($result_brand->num_rows>=1) {
      $row_brand = $result_brand->fetch_assoc();
      $crm_machine_brand_path_old=$row_brand['fullpath'];
    }
  }

  $crm_machine_brand_path_new='';
  $crm_machine_brand_id_new=intval($row_new['crm_machine_brand_id']);
  if ($crm_machine_brand_id_new>0) {
    $sql_brand=str_replace('[[%1]]', $crm_machine_brand_id_new, $sql_brand_template);
    $result_brand = $db_link->query($sql_brand);        
    if (!$result_brand) debug_mail(false,'error sql',$sql_brand);
    if (!$result_brand) die('sql error');
    if ($result_brand->num_rows>=1) {
      $row_brand = $result_brand->fetch_assoc();
      $crm_machine_brand_path_new=$row_brand['fullpath'];
    }
  }
  




  $sxolio_log='';
  
  if (trim_gks($row_old['crm_machine_name']) != trim_gks($row_new['crm_machine_name'])) 
    $sxolio_log.=gks_lang('Όνομα').': <b>'.(isset($row_old['crm_machine_name']) ? $row_old['crm_machine_name'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['crm_machine_name']) ? $row_new['crm_machine_name'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['crm_machine_descr']) != trim_gks($row_new['crm_machine_descr'])) 
    $sxolio_log.=gks_lang('Περιγραφή').':<br>'.(isset($row_old['crm_machine_descr']) ? ($row_old['crm_machine_descr']) : '').'<br>[[-r]]<br>'.
    ''.(isset($row_new['crm_machine_descr']) ? ($row_new['crm_machine_descr']) : '').''.'<br>';

  if (trim_gks($row_old['crm_machine_serial_number']) != trim_gks($row_new['crm_machine_serial_number'])) 
    $sxolio_log.=gks_lang('Serial Number').': <b>'.(isset($row_old['crm_machine_serial_number']) ? $row_old['crm_machine_serial_number'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['crm_machine_serial_number']) ? $row_new['crm_machine_serial_number'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['product_descr_p']) != trim_gks($row_new['product_descr_p'])) 
    $sxolio_log.=gks_lang('Είδος').': <b><a href="admin-products-item.php?id='.$row_old['crm_machine_product_id'].'">'.(isset($row_old['product_descr_p']) ? $row_old['product_descr_p'] : '').'</a></b> [[-r]] '.
    '<b><a href="admin-products-item.php?id='.$row_new['crm_machine_product_id'].'">'.(isset($row_new['product_descr_p']) ? $row_new['product_descr_p'] : '').'</a></b>'.'<br>';

  if ($crm_machine_brand_path_old != $crm_machine_brand_path_new) 
    $sxolio_log.=gks_lang('Μάρκα').': <b><a href="admin-product-brands-item.php?id='.$crm_machine_brand_id_old.'">'.$crm_machine_brand_path_old.'</a></b> [[-r]] '.
    '<b><a href="admin-product-brands-item.php?id='.$crm_machine_brand_id_new.'">'.$crm_machine_brand_path_new.'</a></b>'.'<br>';

  if (trim_gks($row_old['crm_machine_user_id']) != trim_gks($row_new['crm_machine_user_id'])) 
    $sxolio_log.=gks_lang('Πελάτης').': <b><a href="admin-users-item.php?id='.$row_old['crm_machine_user_id'].'">'.trim_gks($row_old['gks_nickname']).'</a></b> [[-r]] '.
    '<b><a href="admin-users-item.php?id='.$row_new['crm_machine_user_id'].'">'.trim_gks($row_new['gks_nickname']).'</a></b>'.'<br>';

  if (trim_gks($row_old['users_extra_address_id']) != trim_gks($row_new['users_extra_address_id'])) 
    $sxolio_log.=gks_lang('Τόπος').': <b>'.($row_old['users_extra_address_id']==-1 ? 'Βασική' : '<a href="admin-users-extra_address-item.php?id='.$row_old['users_extra_address_id'].'">'.trim_gks($row_old['ea_name']).'</a>').'</b> [[-r]] '.
    '<b>'.($row_new['users_extra_address_id']==-1 ? gks_lang('Βασική') : '<a href="admin-users-extra_address-item.php?id='.$row_new['users_extra_address_id'].'">'.trim_gks($row_new['ea_name']).'</a>').'</b>'.'<br>';


    
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_crm_machine',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;
  


  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_crm_machine_log (crm_machine_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    
    //$return = array('success' => false, 'message' => base64_encode($sql));
    //echo json_encode($return); die();  
     
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
  }  
  

}

gks_plugins_functions_run('admin_crm_machine_item_exec_after',array(
  'id'=>&$id,
  'crm_machine_user_id'=>&$crm_machine_user_id,
  'is_new_rec'=>&$is_new_rec,
  'row_old'=>&$row_old,
  'row_custom_old'=>&$row_custom_old,
));

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

