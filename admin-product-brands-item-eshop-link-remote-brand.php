<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$product_brand_id=0; if (isset($_POST['product_brand_id'])) $product_brand_id=intval($_POST['product_brand_id']);
if ($product_brand_id<=0) {
  debug_mail(false,'the product_brand_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' product_brand_id.'));
  echo json_encode($return); die();  } 

$eshop_id=0; if (isset($_POST['eshop_id'])) $eshop_id=intval($_POST['eshop_id']);
if ($eshop_id<=0) {
  debug_mail(false,'the eshop_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' eshop_id.'));
  echo json_encode($return); die();  } 

$pluginname=''; if (isset($_POST['pluginname'])) $pluginname=trim_gks(base64_decode($_POST['pluginname']));
if ($pluginname=='') {
  debug_mail(false,'the pluginname is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' pluginname.'));
  echo json_encode($return); die();  } 

$remote_brand_id=0; if (isset($_POST['remote_brand_id'])) $remote_brand_id=intval($_POST['remote_brand_id']);
if ($remote_brand_id<=0) {
  debug_mail(false,'the remote_brand_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' remote_brand_id.'));
  echo json_encode($return); die();  } 


  
$my_page_title=gks_lang('Σύνδεση μάρκας με ID: [1] με το eshop: [2] με τη remote μάρκα:[3]');
$my_page_title=str_replace('[1]', $product_brand_id, $my_page_title);
$my_page_title=str_replace('[2]', $eshop_id, $my_page_title);
$my_page_title=str_replace('[3]', $remote_brand_id, $my_page_title);


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops','edit',$eshop_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="select * from gks_woo_brands where product_brand_id=".$product_brand_id." and eshop_id=".$eshop_id." and remote_brand_id=".$remote_brand_id." and pluginname='".$db_link->escape_string($pluginname)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 
if ($result->num_rows>=1) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η σύνδεση υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();  }   



$sql="select * from gks_woo_brands where product_brand_id=".$product_brand_id." and eshop_id=".$eshop_id." and pluginname='".$db_link->escape_string($pluginname)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 
if ($result->num_rows>=1) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η μάρκα είναι ήδη συνδεδεμένη με κάποια μάρκα αυτού του eshop για αυτό το πρόσθετο')));
  echo json_encode($return); die();  }


$sql="select * from gks_eshops where id_eshop=".$eshop_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 
if ($result->num_rows==0) {
  debug_mail(false,'eshop not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το eshop')));
  echo json_encode($return); die();  }   

$row = $result->fetch_assoc();
$eshop_name=$row['eshop_name'];
$eshop_url=$row['eshop_url'];

$sql="delete from gks_woo_brands where eshop_id=".$eshop_id." and remote_brand_id=".$remote_brand_id." and pluginname='".$db_link->escape_string($pluginname)."'";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 



$sql="insert into gks_woo_brands (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
product_brand_id,eshop_id,remote_brand_id,pluginname
) values ( 
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
".$product_brand_id.",".$eshop_id.",".$remote_brand_id.",
'".$db_link->escape_string($pluginname)."'
)";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 

$id_woo_brand = $db_link->insert_id; 


$row_html=
'<tr class="eshoplink_tr_new" data-id="'.$id_woo_brand.'">'.
  '<th scope="row" nowrap align="right" class="mytdcm eshoplink_aa">*</td>'.       
  '<td nowrap class="mytdcm">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_eshoplink_delete_after|'.$id_woo_brand.'" data-id="'.$id_woo_brand.'" data-model="gks_woo_brands">'.
  '</td>'.
  '<td class="mytdcm"><a href="admin-eshop-item.php?id='.$eshop_id.'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
  '<td class="mytdcml">'.$eshop_name.'</td>'.
  '<td class="mytdcml"><a href="'.$eshop_url.'" target="_blank">'.$eshop_url.'</a></td>'.
  '<td class="mytdcm" nowrap>';
  
  if ($pluginname=='berocket') $row_html.= '<img src="/my/img/berocket.png" style="height:32px;" class="tooltipster" title="'.gks_lang('Brands for WooCommerce | Από BeRocket').'">';
  else if ($pluginname=='woocommercebrand') $row_html.= '<img src="/my/img/woocommerce.png" style="max-height:32px;max-width:50px;" class="tooltipster" title="'.gks_lang('Woocomerce Brands | Από Woocomerce').'">'; 
  else if (startwith($pluginname,'gks-bai-')) {
    $gks_bai_title='';
    foreach (GKS_ESHOP_BRANDS_TAXONOMY as $brand_as_idiotita) {
      if ('gks-bai-'.$brand_as_idiotita['taxonomy']==$pluginname) {
        $gks_bai_title=$brand_as_idiotita['name'];
      }
    } 
    $row_html.= '<i class="fab fa-wordpress tooltipster" style="font-size: 32px;" title="'.$gks_bai_title.'"></i>';
  } else $row_html.= $pluginname;
  
  $row_html.='</td>'.
  '<td class="mytdcm" nowrap><a href="'.$eshop_url;
  if ($pluginname=='berocket') $row_html.='/wp-admin/term.php?taxonomy=berocket_brand&post_type=product&tag_ID='.$remote_brand_id;
  else if ($pluginname=='woocommercebrand') $row_html.='/wp-admin/term.php?taxonomy=product_brand&post_type=product&tag_ID='.$remote_brand_id;
  else if (startwith($pluginname,'gks-bai-')) $row_html.='/wp-admin/term.php?taxonomy=pa_brand&post_type=product&tag_ID='.$remote_brand_id;
  else $row_html.='/wp-admin/';
  
  $row_html.='" target="_blank">'.$remote_brand_id.'</a></td>'.
  '<td class="mytdcm" nowrap><i class="eshop_sync fas fa-sync-alt tooltipster" data-eshop_id="'.$eshop_id.'" title="'.gks_lang('Συγχρονισμός τώρα').'"></i></td>'.
  '<td class="mytdcm" nowrap></td>'.
'</tr>';




$return = array('success' => true, 'message' => base64_encode('OK'), 'row_html' => base64_encode($row_html));
echo json_encode($return); die(); 
