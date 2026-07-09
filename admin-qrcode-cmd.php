<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Εντολή από Σάρωση QR Code');
db_open();
//stat_record();

$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks(base64_decode($_POST['cmd']));
if (in_array($cmd,['delete','add','new'])) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','add',-1);
} else if (in_array($cmd,['createqr'])) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__downloads','view',0);
}
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$url=''; if (isset($_POST['url'])) $url=trim_gks(base64_decode($_POST['url']));
$format=''; if (isset($_POST['format'])) $format=trim_gks(base64_decode($_POST['format']));
$aa=''; if (isset($_POST['aa'])) $aa=intval($_POST['aa']);
$recid=''; if (isset($_POST['recid'])) $recid=intval($_POST['recid']);
$last_recid=''; if (isset($_POST['last_recid'])) $last_recid=intval($_POST['last_recid']);
$barcode_type_id=56;//QRCODE
if (isset($_POST['barcode_type_id'])) $barcode_type_id=intval($_POST['barcode_type_id']);


$ret_recid=0;
$recs=[];
switch ($cmd) {
	case 'delete':
		if ($recid<=0) {
			debug_mail(false,'cmd delete delete not set');
			$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το recid')));
			echo json_encode($return); die();}	
		
		$sql="delete from gks_qrcode_scan where id_qrcode_scan=".$recid;
		$result = $db_link->query($sql);        
		  if (!$result) {
			debug_mail(false,'error sql',$sql);
			$return = array('success' => false, 'message' => base64_encode('sql error'));
			echo json_encode($return); die();}
		break;
	case 'add':
		if ($url=='') {
			debug_mail(false,'cmd add url not set');
			$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το url')));
			echo json_encode($return); die();}		
		$sql="insert into gks_qrcode_scan (
		mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
		app_id,mytext,format,result
		) values (
		now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
		'".$db_link->escape_string(gks_lang('Σελίδα'))."',
		'".$db_link->escape_string($url)."',
		'".$db_link->escape_string($format)."',
		'new'
		)";
		$result = $db_link->query($sql);        
		  if (!$result) {
			debug_mail(false,'error sql',$sql);
			$return = array('success' => false, 'message' => base64_encode('sql error'));
			echo json_encode($return); die();}
		$ret_recid = $db_link->insert_id; 
		
		break;
	case 'new':
		$myaa=$aa;
		$sql="select * from gks_qrcode_scan 
		where user_id_edit in (0,2,".$my_wp_user_id.") 
		and id_qrcode_scan>".$last_recid."
		order by id_qrcode_scan";
		//echo '<pre>'.$sql;die();
		$result = $db_link->query($sql);  
		if (!$result) {
			debug_mail(false,'error sql',$sql);
			$return = array('success' => false, 'message' => base64_encode('sql error'));
			echo json_encode($return); die();}
	    
		
		while ($row = $result->fetch_assoc()) {
			$recs[]=array(
				'recid' => intval($row['id_qrcode_scan']),
				'url'=> trim_gks($row['mytext']),
				'format'=> trim_gks($row['format']),
				'app_id'=> trim_gks($row['app_id']),
			);
		}
					 

		break;
		
	case 'createqr':
	  $pngAbsoluteFilePath='';
	  if ($url=='') {
	    $return = array('success' => true, 'message' => base64_encode('OK'), 'qrhtml' => '');
	    echo json_encode($return); die();		
	  }
	  //echo '<pre>ddddd'.$barcode_type_id;die();
	  if (in_array($barcode_type_id,[0,56])) { //qrcode
		  include_once('vendor_inc/phpqrcode/qrlib.php');
		

  		$fileName = 'qr_code_temp_'.md5($url).'.png';
  	  
  	  $pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
  	  $urlRelativeFilePath = GKS_SITE_URL.'my/temp/'.$fileName;
  	  
  	  // generating
  	  if (!file_exists($pngAbsoluteFilePath)) {
  	    //QRcode::png($codeContents, $pngAbsoluteFilePath);
  	    QRcode::png($url, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 0);
  	
  	//    how to configure pixel "zoom" factor
  	//    QRcode::png($codeContents, $tempDir.'007_1.png', QR_ECLEVEL_L, 1);
  	//    QRcode::png($codeContents, $tempDir.'007_2.png', QR_ECLEVEL_L, 2);
  	//    QRcode::png($codeContents, $tempDir.'007_3.png', QR_ECLEVEL_L, 3);
  	//    QRcode::png($codeContents, $tempDir.'007_4.png', QR_ECLEVEL_L, 4);
  	
  	//    frame config values below 4 are not recomended !!!
  	//    QRcode::png($codeContents, $tempDir.'008_4.png', QR_ECLEVEL_L, 3, 4);  
  	//    QRcode::png($codeContents, $tempDir.'008_6.png', QR_ECLEVEL_L, 3, 6);
  	//    QRcode::png($codeContents, $tempDir.'008_12.png', QR_ECLEVEL_L, 3, 10);
  	        
  	  } 
  	  if (file_exists($pngAbsoluteFilePath)) {
  	    $return = array('success' => true, 
  	    'message' => base64_encode('OK'), 
  	    'qrhtml' => base64_encode('<img src="'.$urlRelativeFilePath.'" style="max-width:100%;"/>'),
  	    );
  	  } else {
  	  	$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το QR Code')));
  	  }
	    	  
	  } else {
	    $urlRelativeFilePath='';
	    $sql="select barcode_type_code from gks_barcodes_types where id_barcode_type=".$barcode_type_id;
  		$result = $db_link->query($sql);  
  		if (!$result) {
  			debug_mail(false,'error sql',$sql);
  			$return = array('success' => false, 'message' => base64_encode('sql error'));
  			echo json_encode($return); die();}	    
	    if ($result->num_rows>0) {
	      $row=$result->fetch_assoc();
	      $barcodetype=trim_gks($row['barcode_type_code']);
	      if ($barcodetype!='') {
	        $ret=gks_barcode_generate($barcodetype,$url);
	        //echo '<pre>sssss ';print_r($ret);die();
	        if ($ret['url']!='') {
	          $urlRelativeFilePath=$ret['url'];
	        } else {
	          $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το QR Code').'<br>'.$ret['error']));  
	          echo json_encode($return); die();	
	        }
	      }
	    }

      if ($urlRelativeFilePath!='') {
  	    $return = array('success' => true, 
  	    'message' => base64_encode('OK'), 
  	    'qrhtml' => base64_encode('<img src="'.$urlRelativeFilePath.'" style="max-width:100%;"/>'),
  	    );
  	  } else {
  	  	$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το QR Code')));
  	  }
	  }
  	echo json_encode($return); die();		
	
		break;		
	default:
		debug_mail(false,'cmd not found');
		$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εντολή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
		echo json_encode($return); die();	
		break;
	
}
 
$return = array('success' => true, 'message' => base64_encode('OK'), 'ret_recid' => $ret_recid, 'recs' => $recs);
echo json_encode($return); die();


echo '<pre>'.$cmd;die();
