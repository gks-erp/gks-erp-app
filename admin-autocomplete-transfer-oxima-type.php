<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 1 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση transfer τύπου οχήματος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_transfer_oxima_type','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$transfer_id=0;if (isset($_GET['transfer_id'])) $transfer_id=intval($_GET['transfer_id']);

$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
} 
//print '<pre>';
//print_r($term_array);

$sql="SELECT gks_transfer_oxima_type.*
FROM (gks_transfer_oxima_type 
LEFT JOIN (
  SELECT transfer_oxima_type_id, 
  transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
  transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
  FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id)

";

if ($transfer_id>0) {
  $sql.=" LEFT JOIN (
    SELECT gks_transfer_oximatype2transfer.transfer_oxima_type_id
    FROM gks_transfer_oximatype2transfer
    WHERE gks_transfer_oximatype2transfer.transfer_id=".$transfer_id."
    union 
    SELECT gks_transfer_oxima_type.id_transfer_oxima_type as transfer_oxima_type_id
    FROM gks_transfer_oxima_type 
    LEFT JOIN gks_transfer_oximatype2transfer ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oximatype2transfer.transfer_oxima_type_id
    WHERE gks_transfer_oximatype2transfer.transfer_oxima_type_id Is Null
  )  AS thistransfer_oximata_types ON gks_transfer_oxima_type.id_transfer_oxima_type = thistransfer_oximata_types.transfer_oxima_type_id
  ";
}
$sql.=" where gks_transfer_oxima_type.transfer_oxima_type_disable=0 ";

if ($transfer_id>0) {
  $sql.=" and (thistransfer_oximata_types.transfer_oxima_type_id>0)";
}




$sql.=' and (';
 
$mywhere='';
foreach ($term_array as $value) {
  $value_en = greekkeybord($value);
  $mywhere.=" (
  gks_transfer_oxima_type.transfer_oxima_type_descr like '%".$db_link->escape_string($value)."%' or
                          transfer_oxima_type_descr_en_US like '%".$db_link->escape_string($value)."%' or
  gks_transfer_oxima_type.transfer_oxima_type_site_text like '%".$db_link->escape_string($value)."%' or
                          transfer_oxima_type_site_text_en_US like '%".$db_link->escape_string($value)."%' or
  gks_transfer_oxima_type.transfer_oxima_type_comments like '%".$db_link->escape_string($value)."%' or
  
  gks_transfer_oxima_type.transfer_oxima_type_descr like '%".$db_link->escape_string($value_en)."%' or
                          transfer_oxima_type_descr_en_US like '%".$db_link->escape_string($value_en)."%' or
  gks_transfer_oxima_type.transfer_oxima_type_site_text like '%".$db_link->escape_string($value_en)."%' or
                          transfer_oxima_type_site_text_en_US like '%".$db_link->escape_string($value_en)."%' or
  gks_transfer_oxima_type.transfer_oxima_type_comments like '%".$db_link->escape_string($value_en)."%' 
  ) and ";
} 
  
if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
$sql.=$mywhere.")  order by gks_transfer_oxima_type.transfer_oxima_type_descr
limit 1000"; 



$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
$gks_mode=''; if (isset($_GET['mode'])) $gks_mode=trim_gks($_GET['mode']);

$data=array();
while ($row = $result->fetch_assoc()) {
  $data[$row['id_transfer_oxima_type']]=$row;
}




foreach ($data as $row) {

  $fount_count++;
  $descr=trim_gks($row['transfer_oxima_type_descr']);
//  if ($descr=='') {
//    $descr=trim_gks(mb_substr($row['product_descr'],0,100));
//  }
  if (mb_strlen($descr)>100) $descr=mb_substr($descr,100).'...';
  //$descr=$row['product_code'].($descr=='' ? '' : ' '.$descr);
  
  $myimgurl=trim_gks($row['transfer_oxima_type_photo'].'');
  if ($myimgurl == '') {
    $thump_url='/my/img/product.png';
    $photo_url='';
  } else {
    $thump_url=$myimgurl;
    $mydir = dirname($myimgurl);
    if (endwith($mydir,'/thumbnail')) {
      $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
    } else {
      $photo_url=$myimgurl;
    }
  }
  $item['thump']=$thump_url;
  $item['photo']=$photo_url;


  if ($gks_mode=='photo') {
    $out[] = array('id' => $row['id_transfer_oxima_type'], 'value' => $descr, 'photo'=> $photo_url, 'thump' => $thump_url);
  } else {
    $out[] = array('id' => $row['id_transfer_oxima_type'], 'value' => $descr);
  }

}

//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



