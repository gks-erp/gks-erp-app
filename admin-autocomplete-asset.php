<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$from_pos=false; if (isset($_GET['from_pos'])) $from_pos=intval($_GET['from_pos'])==1;

if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);

if (strpos($term, ' - ') !== false) {
  $term= substr($term, 0,strpos($term, ' - '));
}

$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) ==0 && $from_pos==false) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση παγίου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT gks_assets.*, 
gks_assets.asset_last_warehouse_id, gks_warehouses.warehouse_name,
onsss.asset_id
FROM (gks_assets
LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse)
LEFT JOIN (
  SELECT gks_assets_service.asset_id
  FROM gks_assets_service
  WHERE (gks_assets_service.mydate_return Is Null or (mydate_return is not null and isconfirm=0))
  GROUP BY gks_assets_service.asset_id
)  AS onsss ON gks_assets.id_asset = onsss.asset_id
WHERE asset_title<> '' 
and asset_disable = 0 
and (asset_title like '%".$db_link->escape_string($term)."%'
or asset_code like '%".$db_link->escape_string($term)."%'
or asset_serialnumber like '%".$db_link->escape_string($term)."%'
)";
if (!isset($_GET['andservice'])) $sql.=" and (onsss.asset_id is null)";

if (isset($_GET['av'])) $sql.=" and (gks_assets.asset_last_mixani_id<=0 or gks_assets.asset_last_mixani_id is null)";
if (isset($_GET['uav'])) $sql.=" and (gks_assets.asset_last_user_id<=0 or gks_assets.asset_last_user_id is null)";
if (isset($_GET['oxima'])) $sql.=" and gks_assets.asset_type in (26)";
if (isset($_GET['sff'])) $sql.=" and gks_assets.asset_type in (1,6,7)";
if (isset($_GET['printers'])) $sql.=" and gks_assets.asset_type in (2,5,8,9,10,11,15)";
if (isset($_GET['printersds'])) $sql.=" and gks_assets.asset_type in (2,5,8,15)";
if (isset($_GET['tam'])) $sql.=" and gks_assets.asset_type in (16)";
if (isset($_GET['ergas'])) {
  //$sql.=" and gks_assets.asset_last_warehouse_id = ".intval($_GET['ergas']);
}

if (isset($_GET['transfer_id']) and isset($_GET['transfer_oxima_type_id'])) {
  $sql.=" and gks_assets.id_asset in (
    select asset_id from gks_transfer_oxima2type2transfer where transfer_id=".intval($_GET['transfer_id'])." and transfer_oxima_type_id=".intval($_GET['transfer_oxima_type_id'])."
  ) ";
  
}


if (isset($_GET['viva_terminal'])) $sql.=" and gks_assets.viva_terminal_id<>''";

$assets_iris=false;
if (isset($_GET['pawid'])) {
  switch (intval($_GET['pawid'])) {
    case 1: //viva
      $sql.=" and gks_assets.viva_terminal_id<>''";
      break;
    case 2: //megeftpos
      $sql.=" and gks_assets.megeftpos_terminal_id<>''";
      break;
    case 3: //mellon
      $sql.=" and (gks_assets.mellon_id<>'' and gks_assets.mellon_terminal_id<>'')";
      break;
    case 4: //Cardlink  
      $sql.=" and gks_assets.cardlink_terminal_id<>''";
      break;
    case 5: //epay  
      $sql.=" and (gks_assets.epay_id<>'' and gks_assets.epay_terminal_id<>'')";
      break;
    case 6: //worldline  
      $sql.=" and (gks_assets.worldline_id<>'' and gks_assets.worldline_terminal_id<>'')";
      break;
    case 7: //nexi
      $sql.=" and (gks_assets.nexi_id<>'' and gks_assets.nexi_terminal_id<>'')";
      break;
    case 100: //IRIS
      $sql.=" and (1=2)";
      $assets_iris=true;
      break;
    
    default:
    
  }  
  
}


$sql.=" 
order by asset_code, asset_title
limit 1000";

//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die();
}
$recs=[];
while ($row = $result->fetch_assoc()) {
	$row['max_zita']=0;
	$recs[]=$row;
}

//$sql="SELECT tameiaki_mitroo, Max(zita_number) AS mymax FROM gks_esend_zita GROUP BY tameiaki_mitroo";
//$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  die();
//}
//while ($row2 = $result->fetch_assoc()) {
//	foreach ($recs as &$row) {
//		if (strtolower($row2['tameiaki_mitroo'])==strtolower($row['asset_serialnumber'])) {
//			$row['max_zita']=intval($row2['mymax']);
//			break;
//		}
//	}
//	unset($row);
//}

////$sql="SELECT asset_id, Max(zita_number) AS mymax FROM gks_tameiakes_zita GROUP BY asset_id";
////$result = $db_link->query($sql);
////if (!$result) {
////  debug_mail(false,'error sql',$sql);
////  die();
////}
////while ($row2 = $result->fetch_assoc()) {
////	foreach ($recs as &$row) {
////		if ($row2['asset_id']==$row['id_asset'] and $row2['mymax'] > $row['max_zita']) {
////			$row['max_zita']=intval($row2['mymax']);
////			break;
////		}
////	}
////	unset($row);
////}


$simple_value=false; $only_title=false;
if (isset($_GET['sv'])) $simple_value=true;
if (isset($_GET['ot'])) $only_title=true;

$fount_count=0;
$out=array();
if ($assets_iris) {
  //echo '<pre>'.$only_title.'|'.$simple_value;die();
  if ($only_title) {
    $out[] = array('id' => -100, 'value' => gks_lang('Τρέχον'));
  } else if ($simple_value) {
    $out[] = array('id' => -100, 'value' => gks_lang('Τρέχον'), 'asset_type'=>-100, 'asset_last_warehouse_id'=>0,'warehouse_name'=>'--', 'max_zita' => 0);
  } else {
    $out[] = array('id' => -100, 'value' => gks_lang('Τρέχον'), 'asset_type'=>-100, 'asset_last_warehouse_id'=>0,'warehouse_name'=>'--', 'max_zita' => 0);
  }  
  
}

foreach ($recs as $row) {
  $fount_count++;
  if ($only_title) {
    $out[] = array('id' => $row['id_asset'], 'value' => $row['asset_title']);
  } else if ($simple_value) {
    $out[] = array('id' => $row['id_asset'], 'value' => $row['asset_code']. (trim_gks($row['asset_serialnumber'])!='' ?' - '.$row['asset_serialnumber'] : ''), 'asset_type'=>$row['asset_type'], 'asset_last_warehouse_id'=>$row['asset_last_warehouse_id'],'warehouse_name'=>$row['warehouse_name'], 'max_zita' => $row['max_zita']);
  } else {
    $out[] = array('id' => $row['id_asset'], 'value' => $row['asset_code'].' - '.$row['asset_title'].' - '.$row['asset_serialnumber'], 'asset_type'=>$row['asset_type'], 'asset_last_warehouse_id'=>$row['asset_last_warehouse_id'],'warehouse_name'=>$row['warehouse_name'], 'max_zita' => $row['max_zita']);
  }
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



