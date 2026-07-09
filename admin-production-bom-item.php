<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

//https://test.easyfilesselection.com/my/admin-production-bom-item.php?id=10001

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_bom',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_production_bom_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_production_bom','edit',0);




$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }


$gks_custom_prepare = gks_custom_table_item_prepare('gks_production_bom',['from'=>'item']);


$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}


$lang_prepare_gks_monades_metrisis=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_monades_metrisis, array('monada_descr','monada_symbol'));
$sql="SELECT gks_monades_metrisis.id_monada,".
gks_lang_sql_field('monada_descr',$lang_prepare_gks_monades_metrisis).",".
gks_lang_sql_field('monada_symbol',$lang_prepare_gks_monades_metrisis)."
FROM ".$lang_prepare_gks_monades_metrisis['sql']['from1']." gks_monades_metrisis
".$lang_prepare_gks_monades_metrisis['sql']['from2']."
order by monada_sortorder,monada_descr";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$monades=array(); 
while ($row = $result->fetch_assoc()) {
  $monades[]=array(
    'id' => $row['id_monada'],
    'descr' => $row['monada_descr'],
    'symbol' => $row['monada_symbol'],
  );
}


if ($id==-1) {
  $my_page_title=gks_lang('Νέα Συνταγή');
  $row=array();
  
  $row['user_id_add']=0;
  $row['gks_nickname_add']='';
  $row['mydate_add']=null;
  $row['user_id_edit']=0;
  $row['gks_nickname_edit']='';
  $row['mydate_edit']=null;
  $row['myip']='';

  $row['id_production_bom']=-1;
  $row['bom_descr']='';
  $row['reference']='';
  $row['bom_product_id']=0;
  $row['product_code']=0;
  $row['product_photo_p']='';
  $row['product_descr_p']='';
  $row['product_descr_small_p']='';
  
  $row['bom_quantity']=1;
  $row['bom_monada_id']=0;
  $row['bom_disable']=0;
  $row['bom_note']='';

  $row['company_id']=0;
  $row['company_sub_id']=0;
  //na min exei default company
//  if (count($user_companys)>=1) {
//    foreach ($user_companys as $value) {
//      $row['company_id']=$value['id_company'];
//      $row['company_sub_id']=$value['id_company_sub'];
//      $row['company_afm']=$value['company_afm'];
//      break;
//    } 
//  }
  
  $row['product_class']='';
  $row['product_monada_id_org']=0;
  $row['monada_descr_org']='';
  $row['monada_symbol_org']='';
  
//  if (isset($_GET['product_id'])) {
//    $bom_product_id=intval($_GET['product_id']);
    
//  }
  
} else {
    
  $sql ="SELECT gks_production_bom.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
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
  gks_eshop_products.product_class,
  gks_eshop_products.product_monada_id as product_monada_id_org,
  gks_monades_metrisis.monada_descr as monada_descr_org, gks_monades_metrisis.monada_symbol as monada_symbol_org
  FROM ((((gks_production_bom
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_production_bom.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_production_bom.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_products ON gks_production_bom.bom_product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada
  



  where id_production_bom = ".$id;
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

  $my_page_title=gks_lang('Συνταγή').': '.$row['bom_descr'];
  $object_title=$row['bom_descr'];
}
$product_class=trim_gks($row['product_class']);
$bom_monada_id=intval($row['bom_monada_id']);
$product_monada_id_org=intval($row['product_monada_id_org']);
$monada_descr_org=trim_gks($row['monada_descr_org']);
$monada_symbol_org=trim_gks($row['monada_symbol_org']);

$monada_convert_base=array();
gks_monada_convert($bom_monada_id, $product_monada_id_org, $monada_convert_base,array());
//print '<pre>';print_r($monada_convert_base);die();

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();
$nav_active_array=array('production','production_bom');

$sql_eidi="SELECT
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
      WHEN gks_eshop_products.product_photo<>'' THEN
        gks_eshop_products.product_photo
      ELSE
        gks_eshop_products_parent.product_photo
    END
  ELSE gks_eshop_products.product_photo
END as product_photo_p,
CASE
  WHEN gks_eshop_products_variant.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products_variant.product_descr<>'' THEN
        gks_eshop_products_variant.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products_variant.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent_variant.product_descr, gks_eshop_products_variant.product_descr_variable)
          ELSE
            gks_eshop_products_parent_variant.product_descr
        END
    END
  ELSE gks_eshop_products_variant.product_descr
END as product_descr_p_variant,
CASE
  WHEN gks_eshop_products_variant.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products_variant.product_photo<>'' THEN
        gks_eshop_products_variant.product_photo
      ELSE
        gks_eshop_products_parent_variant.product_photo
    END
  ELSE gks_eshop_products_variant.product_photo
END as product_photo_p_variant,

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

gks_eshop_products.product_code,
gks_eshop_products.product_monada_id as product_monada_id_org,
gks_monades_metrisis.monada_descr as monada_descr_org, gks_monades_metrisis.monada_symbol as monada_symbol_org,
gks_eshop_products.product_kostos as product_kostos_org,
gks_production_bom_product.*
FROM ((((gks_production_bom_product
LEFT JOIN gks_eshop_products ON gks_production_bom_product.pbom_product_id = gks_eshop_products.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_variant ON gks_production_bom_product.pbom_variant_product_id = gks_eshop_products_variant.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent_variant ON gks_eshop_products_variant.product_parent_id = gks_eshop_products_parent_variant.id_product)
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada

where production_bom_id>0 and production_bom_id=".$id."
order by pbom_aa";

//echo '<pre>';print_r($sql);die();
$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();
while ($eidos = $result_eidi->fetch_assoc()) {
  $eidos['product_kostos_org']=$eidos['product_kostos_org'];
  
  $pbom_monada_id=intval($eidos['pbom_monada_id']);
  $bom_product_monada_id_org=intval($eidos['product_monada_id_org']);

  $bom_monada_convert=array();
  gks_monada_convert($pbom_monada_id, $bom_product_monada_id_org, $bom_monada_convert,array());
  if ($pbom_monada_id==$bom_product_monada_id_org) {
    $eidos['bom_monada_convert']='';
  } else if ($bom_monada_convert['ok'] and $bom_monada_convert['epi']!=0) {
    $eidos['bom_monada_convert']= '<b>'.myNumberFormatNo0Local($eidos['pbom_quantity'],true).'</b> '.$bom_monada_convert['from_descr'].
      ' = <b>'.myNumberFormatNo0Local($bom_monada_convert['epi']*floatval($eidos['pbom_quantity']),true).'</b> '.$bom_monada_convert['to_descr'];
    $eidos['product_kostos_org']=$eidos['product_kostos_org']*$bom_monada_convert['epi'];
    
  } else {
    $eidos['bom_monada_convert']='<span style="color:red;">'.gks_lang('Δεν μπορεί να γίνει η μετατροπή').'</span>';  
  }
 
  $eidos_array[]=$eidos;
}

$sql_cost="select gks_production_bom_cost.* 
from gks_production_bom_cost
where production_bom_id=".$id."
order by cbom_aa";
//echo '<pre>';print_r($sql);die();
$result_cost = $db_link->query($sql_cost);        
if (!$result_cost) {debug_mail(false,'error sql',$sql_cost); die('sql error');}

$cost_array=array();
while ($cost = $result_cost->fetch_assoc()) {
  $cost_array[]=$cost;
}





$product_variants=array();
if ($product_class=='variable') {
  $sql_variants="SELECT id_product, product_descr_variable
  FROM gks_eshop_products
  WHERE product_parent_id=".$row['bom_product_id']." AND product_disable=0
  order by product_variable_sortorder,product_descr_variable";
  $result_variants = $db_link->query($sql_variants);        
  if (!$result_variants) {debug_mail(false,'error sql',$sql_variants); die('sql error');}
  while ($row_variants= $result_variants->fetch_assoc()) {
    $product_variants[]=array(
      'id'=> intval($row_variants['id_product']),
      'descr'=> trim_gks($row_variants['product_descr_variable']),
    );
  }
}



$calc_res=calc_gks_production_bom_per_product($row['bom_product_id'],$row['bom_quantity'],$monada_convert_base,$eidos_array,$cost_array);
//print '<pre>';print_r($calc_res);die();


include_once('_my_header_admin.php');
?>
<link rel="stylesheet" href="/my/css/admin-production-bom-item.css?v=<?php echo $gks_cache_version;?>" type="text/css">    


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Συνταγή');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Συνταγή');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div id="mypostform">
<div class="container-fluid" >
  <div class="row">
    <div class="col-md-6">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προϊόν προς παραγωγή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>  

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bom_product_id"><?php echo gks_lang('Είδος');?>:</label>
            <div class="col-sm-8">
              <input id="bom_product_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['bom_product_id'];?>">
              <a id="autocomplete_bom_product_id" tabindex="-1" href="admin-products-item.php?id=<?php echo $row['bom_product_id'];?>" style="<?php if ($row['bom_product_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
            </div>
          </div>
          <?php
          $myimgurl=trim_gks($row['product_photo_p'].'');
          
          if ($myimgurl == '') {
            $myimgurl="/my/img/product.png";
            $photo_url='';
          } else {
            $mydir = dirname($myimgurl);
            if (endwith($mydir,'/thumbnail')) {
              $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
            } else {
              $photo_url=$myimgurl;
            }
          }
          ?>
          <div class="form-group row" id="div_product_photo" style="<?php if ($photo_url=='') echo 'display:none;';?>">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bom_product_id"><?php echo gks_lang('Φωτογραφία');?>:</label>
            <div class="col-sm-8" id="div_photo"><?php
              echo '<a href="'.$photo_url.'" class="class_a_product_photo"><img id="img_product_photo" src="'.$myimgurl.'" style="max-width:96px;max-height:96px;"></a>';
            ?>
            </div>
          </div>

          <div class="form-group row" id="div_product_variants" style="<?php if (count($product_variants)<=0) echo 'display:none;';?>">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bom_product_id"><?php echo gks_lang('Παραλλαγές');?>:</label>
            <div class="col-sm-8">
              <div style="font-size: 0.875rem;padding: 0.25rem 0.5rem;height: unset !important;" id="span_product_variants">
                <?php
                $temp='';
                foreach ($product_variants as $value) {
                   $temp.='<a href="admin-products-item.php?id='.$value['id'].'">'.$value['descr'].'</a><br>';
                } 
                echo substr($temp, 0, strlen($temp)-4);
                ?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bom_quantity"><?php echo gks_lang('Ποσότητα');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="bom_quantity"  value="<?php echo $row['bom_quantity'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" style="max-width:100px;">
              <div id="div_monada_conv2" style="<?php if ($bom_monada_id==$product_monada_id_org) echo 'display:none;';?>">
                <small id="span_monada_conv2" class="form-text text-muted">
                <?php
                if ($monada_convert_base['ok'] and $monada_convert_base['epi']!=0) {
                  echo '<b>'.myNumberFormatNo0Local($row['bom_quantity'],true).'</b> '.$monada_convert_base['from_descr'].
                  ' = <b>'.myNumberFormatNo0Local($monada_convert_base['epi']*floatval($row['bom_quantity']),true).'</b> '.$monada_convert_base['to_descr'];
                }
                
                ?>
                </small>
              </div>
              
            </div>
          </div>
          
          <div class="form-group row">
            <label for="bom_monada_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μονάδα Μέτρησης');?>:</label>
            <div class="col-sm-8">
              <select id="bom_monada_id"  class="form-control form-control-sm myneedsave gks_select2" style="width1:" data-product_monada_id_org="<?php echo $product_monada_id_org;?>">
              <option value="0"></option>
              <?php
              foreach ($monades as $mm) {
                echo '<option value="'.$mm['id'].'" ';
                if ($mm['id']==$row['bom_monada_id']) echo ' selected ';
                echo '>'.$mm['descr'].' ('.$mm['symbol'].')</option>';
              }?></select>
              <div id="div_monada_conv" style="<?php if ($bom_monada_id==$product_monada_id_org) echo 'display:none;';?>">
                <small id="span_monada_conv" class="form-text text-muted">
                <?php echo gks_lang('Μονάδα μέτρησης του είδους').': '.$monada_descr_org.($monada_symbol_org=='' ? '' : ' ('.$monada_symbol_org.')').'<br>'.
                gks_lang('Μετατροπή').': ';
                if ($monada_convert_base['ok'] and $monada_convert_base['epi']!=0) {
                  //$quantity_mm=$quantity / $monada_convert_base['epi'];
                  //echo '<pre>';print_r($monada_convert_base);print '</pre>';
                  $out_epi= myNumberFormatNo0Local($monada_convert_base['epi'],true);
                  echo '<b>1</b> '.$monada_convert_base['from_descr'].' = <b>'.$out_epi.'</b> '.$monada_convert_base['to_descr'];

                   
                } else {
                  echo '<span style="color:red;">'.gks_lang('Δεν μπορεί να γίνει η μετατροπή').'</span>';  
                }
                                
                ?>
                </small>
              </div>
            </div>
          </div>



          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bom_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="bom_descr"  value="<?php echo htmlspecialchars_gks($row['bom_descr']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="reference"><?php echo gks_lang('Αναφορά');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="reference"  value="<?php echo htmlspecialchars_gks($row['reference']);?>">
            </div>
          </div>






          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave">
                <option value="0|0"></option>
                <?php
                $company_id_sub_id=$row['company_id'].'|'.$row['company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>

            </div>
          </div> 
          
          <div class="form-group row">
            <label for="bom_note" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="bom_note" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['bom_note']);?></textarea>
            </div>
          </div>          

          
          <div class="form-group row">
            <label for="bom_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="bom_disable"  id="bom_disable" value="1" <?php if ($row['bom_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>                                     

        </div>
      </div>
    </div>
    
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
    
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υλικά που θα χρησιμοποιηθούν');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('product');?> >  
        
          <?php
          $gkscols1='';
          $gkscols2='';
          $gkscols3='';
          $gkscols4='';
          $gkscols5='';
          $gkscols6='';
          $gkscols7='';
          $gkscols8='';

          
          $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-1 gks_items_col';
          $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-2 gks_items_col';
          $gkscols3 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
          $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
          $gkscols6 ='col-6  col-sm-4  col-md-2  col-lg-2 gks_items_col';            
          $gkscols7 ='col-6  col-sm-4  col-md-1  col-lg-2 gks_items_col';            
          $gkscols8 ='col-12 col-sm-4  col-md-2  col-lg-1 gks_items_col';            
        
          
          ?>
          <div class="form-group row gks_eidos_label">
            <div class="<?php echo $gkscols1;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Κωδικός');?></div>
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Περιγραφή');?></div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Παρατηρήσεις');?></div>
            </div>
            <div class="<?php echo $gkscols4;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Ποσότητα');?></div>
            </div>
            <div class="<?php echo $gkscols5;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Μονάδα μέτρησης');?></span></div>
            </div>
            <div class="<?php echo $gkscols6;?>">
              <div class="table-dark gks_eidos_label"><span class="tooltipster" title="<?php echo gks_lang('Κόστος ανά 1 μονάδα μέτρησης');?>"><?php echo gks_lang('Κόστος/μμ');?></span></div>
            </div>
            <div class="<?php echo $gkscols7;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Αφορά την παραλλαγή');?></div>
            </div>
            <div class="<?php echo $gkscols8;?>">
              <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 

          </div> 
          <div id="eidi_table">
            
            
          <?php
          $aa = 0;
          foreach ($eidos_array as $eidos) {
            $aa++;
            ?>
          <div class="form-group row gks_eidos" data-recid="<?php echo $eidos['id_production_bom_product'];?>" data-aa="<?php echo $aa;?>">
            <div class="<?php echo $gkscols1;?>">
              <input type="text" class="form-control form-control-sm gks_code" data-id="<?php echo $eidos['pbom_product_id'];?>" data-aa="<?php echo $aa;?>" 
              style="width:100%;"
              value="<?php echo $eidos['product_code']?>" 
              placeholder="<?php echo gks_lang('Κωδικός');?>"
              >
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="text-left"><?php 
              $product_descr_small=trim_gks($eidos['product_descr_small_p']);  
              if ($product_descr_small!='') {
                $product_descr_small="<table style='max-width:300px' border=0><tr><td>".str_replace('"',"'", $product_descr_small)."</td></tr></table>";
              }
              $myimgurl=trim_gks($eidos['product_photo_p'].'');
              if ($myimgurl == '') {
                $myimgurl="/my/img/product.png";
                echo '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png"><img class="gks_img" style="display:none;" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
              } else {
                $mydir = dirname($myimgurl);
                if (endwith($mydir,'/thumbnail')) {
                  $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
                } else {
                  $photo_url=$myimgurl;
                }
                echo '<a class="lightgalleryitem_bom gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
              }
              echo '<a href="admin-products-item.php?id='.$eidos['pbom_product_id'].'"><i class="gks_product_zoom enterrow fas fa-pen" data-id_product="'.$eidos['pbom_product_id'].'" data-aa="'.$aa.'" title="'.gks_lang('Προβολή Είδους').'"></i></a>';
              echo '<i class="fas fa-info-circle gks_info_descr '.($product_descr_small!='' ? 'tooltipster' : '').'" data-aa="'.$aa.'" title="'.$product_descr_small.'" '.($product_descr_small=='' ? 'style="display:none;"' : '').'></i>';
              echo '<div class="gks_flock form-control-sm gks_descr" data-aa="'.$aa.'">';
                echo htmlspecialchars_gks($eidos['product_descr_p']);
              echo '</div>';               
              //echo '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="'.$aa.'"   placeholder="'.gks_lang('Περιγραφή').'">'.htmlspecialchars_gks($eidos['product_descr_p']).'</textarea>';
              ?>
              
              </div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="<?php echo $aa;?>" placeholder="<?php echo gks_lang('Σχόλιο');?>"><?php echo htmlspecialchars_gks($eidos['pbom_note']);?></textarea>
            </div>
                        
            <div class="<?php echo $gkscols4;?>">
              <input style="text-align:right;" type="number" class="form-control form-control-sm gks_pbom_quantity" data-aa="<?php echo $aa;?>" data-prev-value="<?php echo $eidos['pbom_quantity'];?>" value="<?php if ($eidos['pbom_quantity']!=0) echo $eidos['pbom_quantity'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" placeholder="<?php echo gks_lang('Ποσότητα');?>">
            </div>   
                     
            <div class="<?php echo $gkscols5;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_monada_id" data-aa="<?php echo $aa;?>">
              <option value="0"></option>
              <?php
              foreach ($monades as $mm) {
                echo '<option value="'.$mm['id'].'" ';
                if ($mm['id']==$eidos['pbom_monada_id']) echo ' selected ';
                echo '>'.$mm['descr'].' ('.$mm['symbol'].')</option>';
              }?></select>
              <small style="line-height: 12px;display: block;font-size:11px;" class="gks_bom_monada_convert" data-aa="<?php echo $aa;?>">
                <?php echo $eidos['bom_monada_convert'];?>
              </small>
            </div>
            <div class="<?php echo $gkscols6;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_kostos_type" data-aa="<?php echo $aa;?>" >
                <option value="0" <?php if ($eidos['pbom_kostos_type']==0) echo 'selected';?>><?php echo gks_lang('A Το κόστος του είδους');?> </option>
                <option value="1" <?php if ($eidos['pbom_kostos_type']==1) echo 'selected';?>><?php echo gks_lang('B Ορισμός');?> </option>
              </select>
              <input style="text-align:right;" type="number" class="form-control form-control-sm gks_pbom_kostos_value" data-aa="<?php echo $aa;?>" value="<?php 
              if ($eidos['pbom_kostos_type']==0) {
                if (isset($eidos['product_kostos_org']) and $eidos['product_kostos_org']!=0) echo $eidos['product_kostos_org'];
              } else if ($eidos['pbom_kostos_type']==1) {
                if ($eidos['pbom_kostos_value']!=0) echo $eidos['pbom_kostos_value'];
              }
              ?>" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" 
              placeholder="<?php echo gks_lang('Κόστος');?>" <?php if ($eidos['pbom_kostos_type']==0) echo 'disabled';?> 
              data-kostos_org="<?php if (isset($eidos['product_kostos_org']) and $eidos['product_kostos_org']!=0) echo $eidos['product_kostos_org'];?>">
            </div> 
            <div class="<?php echo $gkscols7;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_variant_product_id" data-aa="<?php echo $aa;?>" style="<?php if ($product_class!='variable') echo 'display:none;';?>">
              <option value="0"></option>
              <?php
              foreach ($product_variants as $value) {
                echo '<option value="'.$value['id'].'" '.($eidos['pbom_variant_product_id']==$value['id'] ? 'selected' : '').'>'.$value['descr'].'</option>';
              }  
              ?>
              </select>
            </div>      
            <?php if ($perm_production_bom_edit) {?>                  
            <div class="<?php echo $gkscols8;?>">
              <div class="text-center gks_icons">
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_eidos" data-aa="<?php echo $aa;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_eidos"  data-aa="<?php echo $aa;?>"></i>
                </div>
                
                
              </div>
            </div>
            <?php } ?>
            
          </div>
          <?php 
          }
          ?>        
        
        
          <div class="row" id="eidi_footer1"></div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
        


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Άλλα Κόστη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cost');?> >  
        
          <?php
          $gkscols_cost1='';
          $gkscols_cost2='';
          $gkscols_cost3='';
          $gkscols_cost4='';
          $gkscols_cost5='';

          
          $gkscols_cost1 ='col-12 col-sm-6  col-md-3  col-lg-3 gks_items_col';
          $gkscols_cost2 ='col-12 col-sm-6  col-md-3  col-lg-4 gks_items_col';
          $gkscols_cost3 ='col-6  col-sm-6  col-md-2  col-lg-2 gks_items_col';
          $gkscols_cost4 ='col-6  col-sm-3  col-md-2  col-lg-2 gks_items_col';
          $gkscols_cost5 ='col-12 col-sm-3  col-md-2  col-lg-1 gks_items_col';
           
          
          ?>
          <div class="form-group row gks_cost_label">
            <div class="<?php echo $gkscols_cost1;?>">
              <div class="table-dark gks_cost_label"><?php echo gks_lang('Περιγραφή');?></div>
            </div>
            <div class="<?php echo $gkscols_cost2;?>">
              <div class="table-dark gks_cost_label"><?php echo gks_lang('Παρατηρήσεις');?></div>
            </div>

            <div class="<?php echo $gkscols_cost3;?>">
              <div class="table-dark gks_cost_label"><?php echo gks_lang('Κόστος');?></div>
            </div>
            <div class="<?php echo $gkscols_cost4;?>">
              <div class="table-dark gks_cost_label"><?php echo gks_lang('Αφορά την παραλλαγή');?></div>
            </div>
            <div class="<?php echo $gkscols_cost5;?>">
              <div class="table-dark gks_cost_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 

          </div> 
          <div id="cost_table">
            
            
          <?php
          $bb = 0;
          foreach ($cost_array as $cost) {
            $bb++;
            ?>
          <div class="form-group row gks_cost_line" data-recid="<?php echo $cost['id_production_bom_cost'];?>" data-bb="<?php echo $bb;?>">
            <div class="<?php echo $gkscols_cost1;?>">
              <input type="text" class="form-control form-control-sm gks_cbom_cost" data-bb="<?php echo $bb;?>" 
              value="<?php echo $cost['cbom_cost']?>" 
              placeholder="<?php echo gks_lang('Περιγραφή');?>"
              >
            </div>
            
            <div class="<?php echo $gkscols_cost2;?>">
              <textarea class="gks_cbom_note form-control form-control-sm" rows="1" data-bb="<?php echo $bb;?>" placeholder="<?php echo gks_lang('Σχόλιο');?>"><?php echo htmlspecialchars_gks($cost['cbom_note']);?></textarea>
            </div>
                        

            <div class="<?php echo $gkscols_cost3;?>">
              <input style="text-align:right;" type="number" class="form-control form-control-sm gks_cbom_kostos_value" data-bb="<?php echo $bb;?>" value="<?php 
              if ($cost['cbom_kostos_value']!=0) echo $cost['cbom_kostos_value'];
              ?>" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" 
              placeholder="<?php echo gks_lang('Κόστος');?>">
            </div> 
            <div class="<?php echo $gkscols_cost4;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_cbom_variant_product_id" data-bb="<?php echo $bb;?>" style="<?php if ($product_class!='variable') echo 'display:none;';?>">
              <option value="0"></option>
              <?php
              foreach ($product_variants as $value) {
                echo '<option value="'.$value['id'].'" '.($cost['cbom_variant_product_id']==$value['id'] ? 'selected' : '').'>'.$value['descr'].'</option>';
              }  
              ?>
              </select>
            </div>       
            <?php if ($perm_production_bom_edit) {?>                 
            <div class="<?php echo $gkscols_cost5;?>">
              <div class="text-center gks_cost_icons">
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_cost" data-bb="<?php echo $bb;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_cost_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_cost"  data-bb="<?php echo $bb;?>"></i>
                </div>
                
                
              </div>
            </div>
            <?php } ?>
          </div>
          <?php 
          }
          ?>        
        
        
          <div class="row" id="cost_footer1"></div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αναφορά','part2');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('report');?> id="gks_report" style="padding: 30px;">  
          <?php echo $calc_res['report'];?>
        </div>
      </div>
    </div>

    
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνολο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('total');?> >  
          <div class="row">
            <div class="col-9 text-right gks_eidos_label">
              <?php echo gks_lang('Υλικά που θα χρησιμοποιηθούν');?>
            </div>
            <div class="col-2 text-right gks_eidos_label" style="font-weight:bold;" id="gsk_base_ylika">
              <?php echo $calc_res['base']['ylika_str'];?>
            </div>
          </div>
          <div class="row">
            <div class="col-9 text-right gks_eidos_label">
              <?php echo gks_lang('Άλλα κόστη');?>
            </div>
            <div class="col-2 text-right gks_eidos_label" style="font-weight:bold;" id="gsk_base_other_cost">
              <?php echo $calc_res['base']['other_cost_str'];?>
            </div>
          </div>
          <div class="row">
            <div class="col-9 text-right gks_eidos_label">
              <?php echo gks_lang('Συνολικό κόστος');?>
            </div>
            <div class="col-2 text-right gks_eidos_label" style="font-weight:bold;" id="gsk_base_total">
              <?php echo $calc_res['base']['total_str'];?>
            </div>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_production_bom'];?>" data-model="gks_production_bom" data-backurl="admin-production-bom.php"><?php echo gks_lang('Διαγραφή');?></button>

      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>
      
    </div>
  </div>
</div>
              
<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
              
      

      <?php 
      echo getObjectRels('gks_production_bom',$id);
      echo getActivityObjectTable('gks_production_bom',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_production_bom','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
                    


              
    </div>

    <div class="col-md-6">
      
               
                
     
      
      
        
       
        




      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php if ($row['id_production_bom']>0) echo $row['id_production_bom'];?>"></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>
        </div>
      </div>
      
    </div>
  
  
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_dialog_object_rel_curr='gks_production_bom';
var from_php_activity_model='gks_production_bom';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;



var last_aa=<?php echo $aa;?>;


var from_php_gkscols1='<?php echo $gkscols1;?>';
var from_php_gkscols2='<?php echo $gkscols2;?>';
var from_php_gkscols3='<?php echo $gkscols3;?>';
var from_php_gkscols4='<?php echo $gkscols4;?>';
var from_php_gkscols5='<?php echo $gkscols5;?>';
var from_php_gkscols6='<?php echo $gkscols6;?>';
var from_php_gkscols7='<?php echo $gkscols7;?>';
var from_php_gkscols8='<?php echo $gkscols8;?>';


var from_php_monades = [];
<?php foreach ($monades as $monada) {
  echo 'from_php_monades.push({id: '.$monada['id'].', descr: $.base64.decode("'.base64_encode($monada['descr']).'"), symbol: $.base64.decode("'.base64_encode($monada['symbol']).'")});'."\n";
}?>
  

var from_php_product_class='<?php echo $product_class;?>';

var from_php_gproduct_variants=[];
<?php foreach ($product_variants as $value) {
  echo 'from_php_gproduct_variants.push({id: '.$value['id'].', descr: $.base64.decode("'.base64_encode($value['descr']).'")});'."\n";
}?>  

var from_php_enter_order=[];
<?php
$gks_user_settings['gks_production_bom']['enter_order']=array(
  'gks_code',
  'gks_comments',
  'gks_pbom_quantity',
  'gks_pbom_monada_id',
  'gks_pbom_kostos_type',
  'gks_pbom_kostos_value',
  'new_row',
);

if (isset($gks_user_settings['gks_production_bom']['enter_order']) and is_array($gks_user_settings['gks_production_bom']['enter_order'])) {
  foreach ($gks_user_settings['gks_production_bom']['enter_order'] as $value) {
    echo 'from_php_enter_order.push(\''.$value.'\');'."\n";
  } 
}
?>

var last_bb=<?php echo $bb;?>;
var from_php_gkscols_cost1='<?php echo $gkscols_cost1;?>';
var from_php_gkscols_cost2='<?php echo $gkscols_cost2;?>';
var from_php_gkscols_cost3='<?php echo $gkscols_cost3;?>';
var from_php_gkscols_cost4='<?php echo $gkscols_cost4;?>';
var from_php_gkscols_cost5='<?php echo $gkscols_cost5;?>';


var from_php_enter_cost_order=[];
<?php
$gks_user_settings['gks_production_bom']['enter_cost_order']=array(
  'gks_cbom_cost',
  'gks_cbom_note',
  'gks_cbom_kostos_value',
  //'gks_cbom_variant_product_id',
  'new_row',
);

if (isset($gks_user_settings['gks_production_bom']['enter_cost_order']) and is_array($gks_user_settings['gks_production_bom']['enter_cost_order'])) {
  foreach ($gks_user_settings['gks_production_bom']['enter_cost_order'] as $value) {
    echo 'from_php_enter_cost_order.push(\''.$value.'\');'."\n";
  } 
}
?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','delete',$id);?>;

tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,
    
  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

       
});
</script>


<script src="js/admin-production-bom-item.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


