<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$product_id=0; if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);
if ($product_id<=0) {
  debug_mail(false,'the product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' product_id.'));
  echo json_encode($return); die();  } 

$eshop_id=0; if (isset($_POST['eshop_id'])) $eshop_id=intval($_POST['eshop_id']);
if ($eshop_id<=0) {
  debug_mail(false,'the eshop_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' eshop_id.'));
  echo json_encode($return); die();  } 

$remote_product_id=0; if (isset($_POST['remote_product_id'])) $remote_product_id=intval($_POST['remote_product_id']);
if ($remote_product_id<=0) {
  debug_mail(false,'the remote_product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' remote_product_id.'));
  echo json_encode($return); die();  } 

$remote_lang='';if (isset($_POST['remote_lang'])) $remote_lang=base64_decode($_POST['remote_lang']);

  
$my_page_title=gks_lang('Σύνδεση είδους με ID: [1] με το eshop: [2] με το remote είδος: [3]');
$my_page_title=str_replace('[1]', $product_id, $my_page_title);
$my_page_title=str_replace('[2]', $eshop_id, $my_page_title);
$my_page_title=str_replace('[3]', $remote_product_id, $my_page_title);

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops','edit',$eshop_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

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
$wpml_enable=intval($row['wpml_enable'])!=0;
if ($wpml_enable==false or $remote_lang=='') {
  $sql="select * from gks_woo_product where product_id=".$product_id." and eshop_id=".$eshop_id." and remote_product_id=".$remote_product_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();  } 
  if ($result->num_rows>=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η σύνδεση υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();  }   
  
  $sql="select * from gks_woo_product where product_id=".$product_id." and eshop_id=".$eshop_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();  } 
  if ($result->num_rows>=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το προϊόν είναι ήδη συνδεδεμένο με κάποιο προϊόν αυτού του eshop')));
    echo json_encode($return); die();  }   

} else {
  $sql="select * from gks_woo_product where product_id=".$product_id." and eshop_id=".$eshop_id." and remote_product_id=".$remote_product_id." and remote_lang='".$db_link->escape_string($remote_lang)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();  } 
  if ($result->num_rows>=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η σύνδεση υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();  }   
  
  $sql="select * from gks_woo_product where product_id=".$product_id." and eshop_id=".$eshop_id." and remote_lang='".$db_link->escape_string($remote_lang)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();  } 
  if ($result->num_rows>=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το προϊόν είναι ήδη συνδεδεμένο με κάποιο προϊόν αυτού του eshop')));
    echo json_encode($return); die();  }   
}


$sql="delete from gks_woo_product where eshop_id=".$eshop_id." and remote_product_id=".$remote_product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 



$sql="insert into gks_woo_product (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
product_id,eshop_id,remote_product_id,remote_lang
) values ( 
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
".$product_id.",".$eshop_id.",".$remote_product_id.",
'".$db_link->escape_string($remote_lang)."'
)";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'sql error',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();  } 

$id_woo_product = $db_link->insert_id; 


$row_html=
'<tr class="eshoplink_tr_new" data-id="'.$id_woo_product.'">'.
  '<th scope="row" nowrap align="right" class="mytdcm eshoplink_aa">*</td>'.       
  '<td nowrap class="mytdcm">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_eshoplink_delete_after|'.$id_woo_product.'" data-id="'.$id_woo_product.'" data-model="gks_woo_product">'.
  '</td>'.
  '<td class="mytdcm"><a href="admin-eshop-item.php?id='.$eshop_id.'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
  '<td class="mytdcml">'.$eshop_name.'</td>'.
  '<td class="mytdcml"><a href="'.$eshop_url.'" target="_blank">'.$eshop_url.'</a></td>'.
  '<td class="mytdcm" nowrap><a href="'.$eshop_url.'/wp-admin/post.php?post='.$remote_product_id.'&action=edit" target="_blank">'.$remote_product_id.'</a></td>'.
  '<td class="mytdcm" nowrap>'.$remote_lang.'</td>'.
  '<td class="mytdcm" nowrap><i class="eshop_sync fas fa-sync-alt tooltipster" data-id_woo_product="'.$id_woo_product.'" title="'.gks_lang('Συγχρονισμός τώρα').'"></i></td>'.
  '<td class="mytdcm" nowrap></td>'.
'</tr>';




$return = array('success' => true, 'message' => base64_encode('OK'), 'row_html' => base64_encode($row_html));
echo json_encode($return); die(); 
