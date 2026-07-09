<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_POST['term'])) die();
$term='';if (isset($_POST['term'])) $term=trim_gks(base64_decode($_POST['term']));$term=str_replace('*', '%', $term);
$id_pos=0;if (isset($_POST['id_pos'])) $id_pos=intval($_POST['id_pos']);if ($id_pos<=0) die();
$page=0;if (isset($_POST['page'])) $page=intval($_POST['page']);if ($page< 0) die();
$fordb=false; if (isset($_POST['fordb'])) $fordb=intval($_POST['fordb'])==1;

//echo '<pre>';echo $term;die();

$my_page_title=gks_lang('Αναζήτηση προϊόντων στο POS page').' '.$page;
if ($fordb) $my_page_title.=' fordb';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$sql="select * from gks_pos where pos_disable=0 and id_pos=".$id_pos;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'pos not found',$sql);
  $return = array('success' => false, 'message' => base64_encode('pos not found'));
  echo json_encode($return); die();}
  
$row_pos = $result->fetch_assoc();

if (1==2) {
  $def_products=array(
    'ids'=>array(
      12283,11016,11018,11014,11015,11019,11144,11000,10995
    
    ),
    'cats'=> array(),
    'brands'=>array(),
    'text'=>array(
      'Backpack Savil Red',
      'AC01',
      'AC02',
      'AC03',
      'AC04',
      'AC05',
      'AC06',
      'AC07',
      'AC08',
      'AC09',
      'AC10',
    ),
  );
  echo '<pre>';echo json_encode($def_products);die();
}

if ($term=='' and $fordb==false) {
  $def_products=array();
  $temp=trim_gks($row_pos['def_products']);
  if ($temp!='') {
    $def_products=json_decode($temp,true);  
  }
}

//echo '<pre>';print_r($def_products);die();



if ($fordb==false) {
  $term_array = array();
  $temp=explode(' ',$term);
  foreach ($temp as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      if (in_array($value, $term_array)==false) $term_array[] = $value;
    }
  } 
}

//print '<pre>';print_r($term_array);die();

$sql="SELECT SQL_CALC_FOUND_ROWS
gks_eshop_products.id_product,
gks_eshop_products.product_code,
gks_eshop_products.product_sku,
gks_eshop_products.product_gtin,
gks_eshop_products.product_upc,
gks_eshop_products.product_ean,
gks_eshop_products.product_isbn,

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
gks_eshop_products.product_monada_id,
gks_eshop_products.product_fpa_base_id,

gks_eshop_products.product_withheldPercentCategory,
gks_eshop_products.product_otherTaxesPercentCategory,
gks_eshop_products.product_stampDutyPercentCategory,
gks_eshop_products.product_feesPercentCategory

FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

where gks_eshop_products.product_disable=0 and gks_eshop_products.product_can_sell=1";
$sql.=" and gks_eshop_products.product_class<>'variable'";


$mywhere='';
$def_where='';
if ($fordb) {
  $recs_per_page=1000;
  
} else {
  $recs_per_page=50;
  if ($term!='') {
    foreach ($term_array as $value) {
      $value_en = greekkeybord($value);
      $mywhere.=" (
      gks_eshop_products.product_code like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_sku like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_gtin like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_upc like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_ean like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_isbn like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_descr like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value)."%' or
      gks_eshop_products.product_object_name like '%".$db_link->escape_string($value)."%' or 
      
      gks_eshop_products.product_code like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_sku like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_gtin like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_upc like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_ean like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_isbn like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_descr like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value_en)."%' or
      gks_eshop_products.product_object_name like '%".$db_link->escape_string($value_en)."%'
      ) and ";
    }
    if (strlen($mywhere)>5) $mywhere=' and ('.substr($mywhere, 0, strlen($mywhere)-5).')';
    
  } else {
    //echo 'hhhhhhhh';die();
    if (isset($def_products['ids']) and count($def_products['ids'])>0) {
      $def_where.="gks_eshop_products.id_product in (".implode(',',$def_products['ids']).") or ";
    }
    if (isset($def_products['text']) and count($def_products['text'])>0) {
      
      foreach ($def_products['text'] as $mytext) {
        if ($mytext!='') {
          $term_array = array();
          $temp=explode(' ',$mytext);
          foreach ($temp as $value) {
            $value=trim_gks($value);
            if ($value!='') {
              if (in_array($value, $term_array)==false) $term_array[] = $value;
            }
          }
          $mywhere_text='';
          foreach ($term_array as $value) {
            $value_en = greekkeybord($value);
            $mywhere_text.=" (
            gks_eshop_products.product_code like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_sku like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_gtin like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_upc like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_ean like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_isbn like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_descr like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value)."%' or
            gks_eshop_products.product_object_name like '%".$db_link->escape_string($value)."%' or 
            
            gks_eshop_products.product_code like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_sku like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_gtin like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_upc like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_ean like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_isbn like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_descr like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value_en)."%' or
            gks_eshop_products.product_object_name like '%".$db_link->escape_string($value_en)."%'
            ) and ";
          }
          if (strlen($mywhere_text)>5) $def_where.='('.substr($mywhere_text, 0, strlen($mywhere_text)-5).') or ';
          
          
        }
        
      }
      
      
    }
    
    
    if ($def_where!='') {
      $def_where=' and ('.substr($def_where, 0, strlen($def_where)-4).')';
    }
    
    
  }
}

$sql.=$mywhere.$def_where." order by gks_eshop_products.product_descr,gks_eshop_products.product_descr_variable,gks_eshop_products.product_code"; 

//if ($temp!='') {
$sql.=" limit ".($page*$recs_per_page).", ".$recs_per_page;
//}


//print '<pre>'.$sql;die();


$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}


$sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
$res_numrows = $db_link->query($sql_numrows);
$row_numrows = $res_numrows->fetch_assoc();
$total_records = intval($row_numrows['found_rows']);

$fount_count=0;
$eidi_array=array();

$data=array();
while ($row = $result->fetch_assoc()) {
  $data[$row['id_product']]=$row;
}


$eidi_array=array();
$list=array();
$aa=0;
foreach ($data as $row) {

  $fount_count++;
  $descr=trim_gks($row['product_descr_p']);
  if (mb_strlen($descr)>100) $descr=mb_substr($descr,100).'...';
  $product_code=trim_gks($row['product_code']);
  //if ($product_code=='') $product_code='--';

  $photo_url='';
  $myimgurl=trim_gks($row['product_photo_p'].'');
  if ($myimgurl != '') {
    $mydir = dirname($myimgurl);
    if (endwith($mydir,'/thumbnail')) {
      $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
    } else {
      $photo_url=$myimgurl;
    }
  }  

  $aa++;
  
  $eidi_array[] = array(
    'product_id' => $row['id_product'], 
    
    'aa'=> $aa,
    'product_price_ekptosi_pososto' => 0,
    'product_quantity' => 1,
    'product_monada_id' =>$row['product_monada_id'], 
    'product_fpa_base_id' =>$row['product_fpa_base_id'], 

    'product_withheldPercentCategory' =>$row['product_withheldPercentCategory'],
    'product_withheldAmount' =>0, //$row['product_withheldAmount'],
    'product_otherTaxesPercentCategory' =>$row['product_otherTaxesPercentCategory'],
    'product_otherTaxesAmount' =>0, //$row['product_otherTaxesAmount'],
    'product_stampDutyPercentCategory' =>$row['product_stampDutyPercentCategory'],
    'product_stampDutyAmount' =>0, //$row['product_stampDutyAmount'],
    'product_feesPercentCategory' =>$row['product_feesPercentCategory'],
    'product_feesAmount' =>0, //$row['product_feesAmount'],
    'product_deductionsAmount' =>0, //$row['product_deductionsAmount'],

    
  );
  

  
  $curr_item= array(
    'id' => intval($row['id_product']), 
    'code' => $product_code, 
    'descr' => $descr,
    'myimgurl'=>$myimgurl,
    //'photo_url'=>$photo_url,
    'price' => 0,
    'vat' => 0,
    'ovat' => 0,
  );
  if ($fordb) {
    $curr_item['descr_conv']=strtolower(greeklish($curr_item['descr']));
    $curr_item['code_conv']=strtolower(greeklish($curr_item['code']));
    $curr_item['sku']=strtolower(greeklish(trim_gks($row['product_sku'])));
    $curr_item['gtin']=trim_gks($row['product_gtin']);
    $curr_item['upc']=trim_gks($row['product_upc']);
    $curr_item['ean']=trim_gks($row['product_ean']);
    $curr_item['isbn']=trim_gks($row['product_isbn']);
    
  }
  
  $list[]=$curr_item;
  
}










////////////////////////////////////

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


$sql="SELECT gks_pos.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, 
gks_users.ma_orofos, gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id,gks_users.ma_nomos_id,
".GKS_WP_TABLE_PREFIX."users.generic_ekprosi,
".GKS_WP_TABLE_PREFIX."users.user_email,
".GKS_WP_TABLE_PREFIX."users.gks_mobile,

gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,gks_country.country_name,gks_nomoi.nomos_descr,
myfirst_name,mylast_name,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to

FROM ((((((((((((((((((((((((gks_pos
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company on gks_pos.pos_company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_pos.pos_company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_pos.def_tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_pos.def_tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_pos.def_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_pos.def_pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_pos.def_user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_pos.def_aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_pos.def_assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_pos.def_crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_pos.def_crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_pos.def_crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_pos.pos_warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_pos.pos_warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id


where id_pos=".$id_pos;

if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
  
if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql tempate',$sql); 
  die('no record found (tempate)');
}
$row_pos=$result->fetch_assoc();

//print '<pre>';print_r($row_pos);die();


unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='acc_inv';
$mybasketarray['id_object'] = -1;
$mybasketarray['company_id']=intval($row_pos['pos_company_id']);
$mybasketarray['company_sub_id']=intval($row_pos['pos_company_sub_id']);
$mybasketarray['inv_acc_journal_id']=intval($row_pos['pos_journal_id']);
$mybasketarray['inv_acc_seira_id']=intval($row_pos['pos_seira_id']);
$mybasketarray['inv_state']=trim_gks('010draft');
$mybasketarray['inv_date']=date('Y-m-d H:i:s'); //2022-05-19 22:32:00
//echo '<pre>'; echo $mybasketarray['inv_date']; die();

$mybasketarray['user']['user_id']=intval($row_pos['def_user_id']);
$mybasketarray['user']['first_name']=trim_gks($row_pos['myfirst_name']);
$mybasketarray['user']['last_name']=trim_gks($row_pos['mylast_name']);
$mybasketarray['user']['email']=trim_gks($row_pos['user_email']);
$mybasketarray['user']['mobile']=trim_gks($row_pos['gks_mobile']);
$mybasketarray['user']['lang']=trim_gks($row_pos['def_user_lang']);
$mybasketarray['user']['ma_odos']=trim_gks($row_pos['ma_odos']);
$mybasketarray['user']['ma_arithmos']=trim_gks($row_pos['ma_arithmos']);
$mybasketarray['user']['ma_orofos']=trim_gks($row_pos['ma_orofos']);
$mybasketarray['user']['ma_perioxi']=trim_gks($row_pos['ma_perioxi']);
$mybasketarray['user']['ma_poli']=trim_gks($row_pos['ma_poli']);
$mybasketarray['user']['ma_tk']=trim_gks($row_pos['ma_tk']);
$mybasketarray['user']['ma_country_id']=intval($row_pos['ma_country_id']);
$mybasketarray['user']['ma_nomos_id']=intval($row_pos['ma_nomos_id']);
$mybasketarray['user']['eponimia']=trim_gks($row_pos['eponimia']);
$mybasketarray['user']['title']=trim_gks($row_pos['title']);
$mybasketarray['user']['afm']=trim_gks($row_pos['afm']);
$mybasketarray['user']['doy']=trim_gks($row_pos['doy']);
$mybasketarray['user']['epaggelma']=trim_gks($row_pos['epaggelma']);
$mybasketarray['address_extra']=-1;
$mybasketarray['destination_data']['name'] = '';
$mybasketarray['destination_data']['phone'] = '';
$mybasketarray['destination_data']['odos'] = '';
$mybasketarray['destination_data']['arithmos'] = '';
$mybasketarray['destination_data']['orofos'] = '';
$mybasketarray['destination_data']['perioxi'] = '';
$mybasketarray['destination_data']['poli'] =  '';
$mybasketarray['destination_data']['tk'] = '';
$mybasketarray['destination_data']['country_id'] = 0;
$mybasketarray['destination_data']['nomos_id'] = 0;
if ($mybasketarray['destination_data']['country_id']==0) $mybasketarray['destination_data']['country_id']=91;



//$mybasketarray['user']['ma_country_id']=91;
$mybasketarray['fiscal_position']=intval($row_pos['def_fiscal_position_id']);
if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;

$mybasketarray['pricelist_id']=intval($row_pos['def_pricelist_id']);
if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
$mybasketarray['coupons']=array();


$mybasketarray['parastatiko']=intval($row_pos['eidos_parastatikou_need_afm']);




  
$mybasketarray['products_need_apostoli'] = 1; //intval($mydata['gks_products_need_apostoli'])!=0;
$mybasketarray['products_varos']= 0; //intval($mydata['gks_products_varos']);
$mybasketarray['products_ogos']= 0; //intval($mydata['gks_products_ogos']);
$mybasketarray['products_ogos_max_x']= 0; //intval($mydata['gks_products_ogos_x']);
$mybasketarray['products_ogos_max_y']= 0; //intval($mydata['gks_products_ogos_y']);
$mybasketarray['products_ogos_max_z']= 0; //intval($mydata['gks_products_ogos_z']);
$mybasketarray['products_need_pliromi']=false;
//if (floatval($mydata['gks_total_price_total'])>0) $mybasketarray['products_need_pliromi']=true;;



//echo '<pre>';
//print $mybasketarray['destination_data']['country_id'];
//die();

$mybasketarray['tropos_apostolis'] = intval($row_pos['def_tropos_apostolis']);
$mybasketarray['tropos_pliromis'] = intval($row_pos['def_tropos_pliromis']);
$mybasketarray['products_total'] = 0; //floatval($mydata['gks_total_price_total']);

$fields_change=array();//$mydata['fields_change'];
$fields_change[0]='';
$fields_change[1]='gks_price';

//for ($i==0; $i <= count($out);$i++) {
//  $fields_change[$i]=
//} 

$fields_change_curr_name='code'; //'gks_quantity'; //trim_gks($mydata['fields_change_curr_name']);
$fields_change_curr_aa=1; //intval($mydata['fields_change_curr_aa']);

//print '<pre>';print_r($mybasketarray);die();


$basket_products_temp =array();
foreach ($eidi_array as &$value) {
  $user_field_change='';
  if ($value['aa'] == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
  $user_change_ekptosi_or_final_net='';
  if (isset($fields_change[$value['aa']])) $user_change_ekptosi_or_final_net=$fields_change[$value['aa']];
  
  $user_ekptosi = floatval($value['product_price_ekptosi_pososto']);  
  
  


  $hotel_check_out_round  = showDate(time(),'Y-d-m',1);
  $hotel_check_in_round = showDate(time()-$value['product_quantity']*24*60*60,'Y-d-m',1);
  if ($mybasketarray['inv_date']!='') {
    $hotel_check_out_round  = date('Y-d-m',strtotime($mybasketarray['inv_date']));
    $hotel_check_in_round = date('Y-d-m',strtotime($mybasketarray['inv_date'])-$value['product_quantity']*24*60*60);
  }

 
  //print '<pre>';print_r($value);die();
  
  $objects=array();
  $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
  $basket_products_temp[$value['aa']]=array(
    'product_id'=>array(
      'id_product'=>$value['product_id'], 
      'product_monada_id' => $value['product_monada_id'], 
      'product_fpa_base_id' => $value['product_fpa_base_id'], 
      'product_sheets'=>0, 
      'product_set' => '',
     ), 
    'objects'=>$objects,
    'user_ekptosi' => 0, //$user_ekptosi,
    'user_final_net' => 0, //floatval($value['product_price_final_all_net']),
    'user_change_ekptosi_or_final_net' => 'gks_ekptosi', //$user_change_ekptosi_or_final_net,
    'user_field_change' => 'gks_ekptosi', //$user_field_change,
    'from_aade_import_user_fpa' => 0, //$value['from_aade_import_user_fpa'],
    'from_aade_import_user_fpa_value' => 0, //$value['from_aade_import_user_fpa_value'],
    
    
    


    
    'other_taxes' => array(
      'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
      'withheldAmount' => floatval($value['product_withheldAmount']),  
      'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
      'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
      'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
      'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
      'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
      'feesAmount' => floatval($value['product_feesAmount']),  
      'deductionsAmount' => floatval($value['product_deductionsAmount']),  
    
    ),
    
  );
}
unset($value);
//print '<pre>';print_r($basket_products_temp);die();  


$mybasketarray['products'] = $basket_products_temp;
$myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());

//print '<pre>';print_r($myproducts);die();  
//$tropos_apostolis=intval($mydata['tropos_apostolis']);
//$tropos_pliromis=intval($mydata['tropos_pliromis']);


$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

$kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
$kostos_pliromis_mode='';  if (isset($mydata['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($mydata['kostos_pliromis_mode']);

if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $mydata['kostos_pliromis'];


$pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];

$products_ogos='';
if ($mybasketarray['products_ogos_max_x']>0 or $mybasketarray['products_ogos_max_y']>0 or $mybasketarray['products_ogos_max_z']>0) {
  $products_ogos = number_format($mybasketarray['products_ogos_max_x'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                   number_format($mybasketarray['products_ogos_max_y'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                   number_format($mybasketarray['products_ogos_max_z'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
  
}
$products_varos='';
if ($mybasketarray['products_varos']>0) $products_varos=number_format($mybasketarray['products_varos'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).' gr';

$eidi=array();


foreach ($mybasketarray['products'] as $aa => $value) {
  $product_price_ekptosi_net=round($value['product_id']['product_price_start_all_net']-$value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $product_price_ekptosi_netfpa=round($value['product_id']['product_price_start_all_net']+$value['product_id']['product_price_start_all_fpa']-$value['product_id']['product_price_final_all_net']-$value['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $product_price_ekptosi_total=round($value['product_id']['product_price_start_all_total']-$value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $product_price_ekptosi_pososto=0;
  if ($value['product_id']['product_price_start_all_net']!=0 and $value['product_id']['product_price_include_vat']==0) {
    $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$value['product_id']['product_price_start_all_net'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
  } else if ($value['product_id']['product_price_start_all_total']!=0 and $value['product_id']['product_price_include_vat']!=0) {
    $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$value['product_id']['product_price_start_all_total'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
  }
  $ekptosi_poso_html='';
  if ($product_price_ekptosi_net!=0) {
    $ekptosi_poso_html=myCurrencyFormat($product_price_ekptosi_net,false,true);
    if ($GKS_BASKET_ROUND_DIAFORA_001) {
      if (abs($product_price_ekptosi_net)<=(1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
        $ekptosi_poso_html=''; //to 0.01 na ginei keno
      }
    } 
  }
  $ekptosi_poso_netfpa_html='';
  if ($product_price_ekptosi_netfpa!=0) {
    $ekptosi_poso_netfpa_html=myCurrencyFormat($product_price_ekptosi_netfpa,false,true);
    if ($GKS_BASKET_ROUND_DIAFORA_001) {
      if (abs($product_price_ekptosi_netfpa)<=(1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
        $ekptosi_poso_netfpa_html=''; //to 0.01 na ginei keno
      }
    } 
  }
  
  

  if (isset($value['user_change_ekptosi_or_final_net']) and $value['user_change_ekptosi_or_final_net']=='gks_ekptosi' and isset($value['user_ekptosi'])) {
    $product_price_ekptosi_pososto=$value['user_ekptosi'];
  }  


  //print '<pre>';print $value['product_id']['product_price_final_all_fpa']; die();
  
  $eidi[] = array(
    'aa' => $aa,
    'product_price_final_peritem_net' => round($value['product_id']['product_price_final_peritem_net'],$GKS_BASKET_CALC_ITEM_DECIMAL),
    'product_price_final_peritem_fpa' => round($value['product_id']['product_price_final_peritem_fpa'],$GKS_BASKET_CALC_ITEM_DECIMAL),
    'product_price_final_all_net' => $value['product_id']['product_price_final_all_net'],
    'product_price_start_all_net' => $value['product_id']['product_price_start_all_net'],
    'product_price_final_all_fpa' => $value['product_id']['product_price_final_all_fpa'],
    'fpa_base_id' => $value['product_id']['product_fpa_base_id'],
    'id_fpa' => $value['product_id']['product_fpa_id_array']['id_fpa_to'],
    'fpa_pososto' => $value['product_id']['product_fpa_id_array']['fpa_pososto'],
    'fpa_descr_print' => $value['product_id']['product_fpa_id_array']['fpa_descr_print'],

    
    'varos' => intval($value['product_id']['product_varos']),
    'ogos_x' => intval($value['product_id']['product_ogos_x']),
    'ogos_y' => intval($value['product_id']['product_ogos_y']),
    'ogos_z' => intval($value['product_id']['product_ogos_z']),
    'need_apostoli' => intval($value['product_id']['product_need_apostoli']),
    'ekptosi_poso_html' => $ekptosi_poso_html,
    'ekptosi_poso_netfpa_html' => $ekptosi_poso_netfpa_html,
    'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
    'product_price_coupon_use' => $value['product_id']['product_price_coupon_use'],
    'product_price_coupon_use_disabled' => $value['product_id']['product_price_coupon_use_disabled'],
    
    
    'other_taxes' => $value['other_taxes'],
  );
  
  //print '<pre>';print_r($value);die();
  
  foreach ($list as &$item_list) {
  
    if ($item_list['id']==$value['product_id']['id_product']) {
      $item_list['price']=$value['product_id']['product_price_final_all_net'] + 
                          $value['product_id']['product_price_final_all_fpa'] + 
                          -$value['other_taxes']['withheldAmount'] + 
                          $value['other_taxes']['otherTaxesAmount'] + 
                          $value['other_taxes']['stampDutyAmount'] + 
                          $value['other_taxes']['feesAmount'] + 
                          $value['other_taxes']['deductionsAmount'];
                          
      $item_list['vat']=$value['product_id']['product_price_final_all_fpa'];
      $item_list['vp']=floatval($value['product_id']['product_fpa_id_array']['fpa_pososto']);
      
      $item_list['ovat']= -$value['other_taxes']['withheldAmount'] + 
                           $value['other_taxes']['otherTaxesAmount'] + 
                           $value['other_taxes']['stampDutyAmount'] + 
                           $value['other_taxes']['feesAmount'] + 
                           $value['other_taxes']['deductionsAmount'];
      
      break;
    }
  } 
  unset($item_list);
  

    
} 


//gks_CheckAFM_Live($mybasketarray);


//print '<pre>sss';print_r($mybasketarray);die();  





//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$list,'total_records'=>$total_records);
echo json_encode($return); die();

echo json_encode($out);



