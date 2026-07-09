<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$paroxos_id=0;if (isset($_POST['paroxos'])) $paroxos_id=intval($_POST['paroxos']);
if ($paroxos_id<=0) {
  debug_mail(false,'the paroxos is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο πάροχος')));
  echo json_encode($return); die();}

$cmd='';if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd=='' or in_array($cmd,['invoice_pending','tf1_get_keys','tf1_create_keys'])==false) {
  debug_mail(false,'the cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή')));
  echo json_encode($return); die();}

switch ($cmd) {   
  case 'invoice_pending':     
    {
    $afms=gks_paroxos_overview_get_afms($paroxos_id);
    if (count($afms)==0) {
      debug_mail(false,'afms not found','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν σχετικές εταιρείες')));
      echo json_encode($return); die();}      
    
    $sql="truncate gks_paroxos_overview_ilyda_invoice_pending;";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    $data=[];$data_cc=0;
    foreach ($afms as $afm) {
      //https://test.vs.gr/api/invoice/pending/by-vat/[VAT]/[pageNum]/[pageSize] (test)
      $curpage=0;$pagesize=1000;$recs=[];
      
      do {
        //if ($paroxos_params['paroxos_mydata_live']) $url=GKS_ILYDA_COM_MODE_LIVE_API; else $url=GKS_ILYDA_COM_MODE_TEST_API;
        $url=GKS_ILYDA_COM_MODE_LIVE_API; 
        $url.='/api/invoice/pending/by-vat/'.$afm['afm'].'/'.$curpage.'/'.$pagesize;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  
        $headers=[];
        $headers[]='Content-Type: text/html; charset=UTF-8';
        $headers[]='Accept: application/json';
        $headers[]='Authorization: Basic '.base64_encode($afm['username'].':'.$afm['password']);  
         
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 

        
        $result=curl_exec($ch);
        $gks_curl_errno=curl_errno($ch);
        $gks_curl_info =curl_getinfo($ch);
        curl_close ($ch);
                
        $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
        if ($gks_curl_http_code!=200) {
          debug_mail(false,'error ylida response',$url);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (1)'));
          echo json_encode($return); die();}           
        
        $response_array = json_decode($result, true);
        if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
          debug_mail(false,'ilyda vs.gr json_decode error',base64_encode($result) .'|||'.$result.'|||'.base64_encode($response) .'|||'.$response);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (2)'));
          echo json_encode($return); die();}
        
        if (isset($response_array['pending']) and is_array($response_array['pending'])) {
          foreach ($response_array['pending'] as $value) {
            $recs[]=$value;
          } 
        }
        if (isset($response_array['total']) and isset($response_array['pageNumber'])) {
          $total=intval($response_array['total']);
          $pageNumber=intval($response_array['pageNumber']);
          if (count($recs)>=$total) {
            break;
          }
        }
        
        //echo '<pre>aaaa url '.$url.'</pre>';
        //echo '<pre>aaaa curpage '.$curpage.'</pre>';
        $curpage++;
      } while (true);

      $data[$afm['afm']]=array(
        'afm'=> $afm['afm'],
        'recs'=>$recs,
      );
      $data_cc+=count($recs);
      //echo '<pre>ssssssss '.count($recs);die();
      
      //echo '<pre>'.$url;die();
    }
    //echo '<pre>dddd';print_r($afms);die();
    
    $save_dir = GKS_SITE_PATH.'/tmp/';
    $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
    $set_filename_s='invoice_pending_'.$set_filename.'-ilyda';
    
    require_once('vendor_inc/Nicer.php');
    
    $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
    <meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
  	</head><body>';
          $obj_nicer = new Nicer($data, true, true);
          $raw_file.=$obj_nicer->render(false);
          $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
          $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
          $raw_file.=json_encode($data,JSON_PRETTY_PRINT);
          $raw_file.='</pre></div>';
    $raw_file.='</body>
    </html>'; 
    file_put_contents($save_dir.$set_filename_s.'.html', $raw_file); 
    
    $sql_insert="insert into gks_paroxos_overview_ilyda_invoice_pending (
    mydate_add,user_id_add,myip,afm,
    invoiceNumber,uid,sellerVatIdentifier,seriesNumber,serialNumber,invoiceId,
    mark,verificationHash,invoiceIssueDate,invoiceState,errorsJson) values ";
    
    $sqls_ins=array();
    foreach ($data as $myafm) {
      foreach ($myafm['recs'] as $rec) {
        $invoiceIssueDate='null';
        $tt=intval(intval($rec['invoiceIssueDate'])/1000);
        if ($tt>0) {
          $invoiceIssueDate="'".date('Y-m-d H:i:s',$tt)."'";
        }
        
        $sqls_ins[]="(now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        '".$db_link->escape_string(trim_gks($myafm['afm']))."',
        '".$db_link->escape_string(trim_gks($rec['invoiceNumber']))."',
        '".$db_link->escape_string(trim_gks($rec['uid']))."',
        '".$db_link->escape_string(trim_gks($rec['sellerVatIdentifier']))."',
        '".$db_link->escape_string(trim_gks($rec['seriesNumber']))."',
        '".$db_link->escape_string(intval($rec['serialNumber']))."',
        '".$db_link->escape_string(trim_gks($rec['invoiceId']))."',
        '".$db_link->escape_string(trim_gks($rec['mark']))."',
        '".$db_link->escape_string(trim_gks($rec['verificationHash']))."',
        ".$invoiceIssueDate.",
        '".$db_link->escape_string(trim_gks($rec['invoiceState']))."',
        '".$db_link->escape_string(trim_gks($rec['errorsJson']))."')";
         
         
        if (count($sqls_ins)>=10) {
          $sql=$sql_insert." ".implode(',',$sqls_ins);
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          $sqls_ins=array();
        } 
         
         
      } 
    }
    
    if (count($sqls_ins)>=1) {
      $sql=$sql_insert." ".implode(',',$sqls_ins);
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
    }     
    
    $return = array('success' => true, 'message' =>  base64_encode('OK'));
    $return['count']=$data_cc;
    $return['file']='admin-get-file.php?fs=tmp&file='.rawurlencode($set_filename_s.'.html');
    echo json_encode($return); die();
    
    break; } 
    
  case 'tf1_get_keys':
    {
      
    $afms=gks_paroxos_overview_get_afms($paroxos_id);
    if (count($afms)==0) {
      debug_mail(false,'afms not found','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν σχετικές εταιρείες')));
      echo json_encode($return); die();}      
    
    //echo '<pre>';print_r($afms);die();
    $per_afm=[];
    foreach ($afms as $afm) {
      $url=GKS_ILYDA_COM_MODE_LIVE_API; 
      $url.='/api/offline-qr/'.$afm['afm'].'/keys';
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
      $headers=[];
      $headers[]='Content-Type: application/json; charset=UTF-8';
      $headers[]='Accept: application/json';
      $headers[]='Authorization: Basic '.base64_encode($afm['username'].':'.$afm['password']);  
       
      curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 

      $result=curl_exec($ch);
      $gks_curl_errno=curl_errno($ch);
      $gks_curl_info =curl_getinfo($ch);
      curl_close ($ch);

      $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
      if ($gks_curl_http_code!=200) {
        debug_mail(false,'error ylida response',$url);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (1)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result));
        echo json_encode($return); die();}           
      
      $response_array = json_decode($result, true);
      if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
        debug_mail(false,'ilyda vs.gr json_decode error',base64_encode($result) .'|||'.$result.'|||'.base64_encode($response) .'|||'.$response);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (2)'));
        echo json_encode($return); die();}
      
      
      $per_afm[]=array(
        'afm'=>$afm['afm'],
        'count'=>count($response_array),
        'keys'=> $response_array,
      );
      //echo '<pre>';print_r($response_array);die();
                     
    }
    $html=[];
    foreach ($per_afm as $myafm) {
      
      $sql="update gks_paroxos_tf1_keys set 
      local_status='ARCHIVE' 
      where local_status<>'ARCHIVE'
      and afm='".$db_link->escape_string($myafm['afm'])."'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}       

      
      $item='Το ΑΦΜ '.$myafm['afm'].' έχει '.$myafm['count'].' '.gks_lang('κλειδιά');
      $mycount=['ISSUED'=>0,'VERIFIED'=>0,'REVOKED'=>0];
/*
        (
            [keyIdentifier] => 09a0ab96-81f4-40d8-a94f-67be403c196e
            [keyVersion] => 59
            [algorithm] => OFFLINE_QR_JWS
            [purpose] => Offline QR
            [status] => ISSUED
            [issuedAt] => 1767180061.4602
            [revokedAt] => 
            [validFrom] => 1767180061.4602
            [validTo] => 1768044061.4602
            [installationVerifiedAt] => 
            [revokeReason] => 
            [linkBaseUrl] => https://test.vs.gr/iv/invoice/offline
        )
*/     
      $valid_keyIdentifiers=[];
      foreach ($myafm['keys'] as $mykey) {
        if (isset($mykey['status'])) {
          $mycount[$mykey['status']]++;
        }
        $issuedAt='null'; if (isset($mykey['issuedAt']) and intval($mykey['issuedAt'])>0) $issuedAt="'".date('Y-m-d H:i:s', intval($mykey['issuedAt']))."'";
        $revokedAt='null'; if (isset($mykey['revokedAt']) and intval($mykey['revokedAt'])>0) $revokedAt="'".date('Y-m-d H:i:s', intval($mykey['revokedAt']))."'";
        $validFrom='null'; if (isset($mykey['validFrom']) and intval($mykey['validFrom'])>0) $validFrom="'".date('Y-m-d H:i:s', intval($mykey['validFrom']))."'";
        $validTo='null'; if (isset($mykey['validTo']) and intval($mykey['validTo'])>0) $validTo="'".date('Y-m-d H:i:s', intval($mykey['validTo']))."'";
        $installationVerifiedAt='null'; if (isset($mykey['installationVerifiedAt']) and intval($mykey['installationVerifiedAt'])>0) $installationVerifiedAt="'".date('Y-m-d H:i:s', intval($mykey['installationVerifiedAt']))."'";
        
        $local_status='NOT_VALID';
        if (intval($mykey['revokedAt'])==0 and 
            intval($mykey['validFrom'])>0 and time()>=intval($mykey['validFrom']) and 
            intval($mykey['validTo'])>0   and time()<=intval($mykey['validTo']) and 
            $mykey['status']=='VERIFIED') {
          $local_status='VALID';      
        }
        
        if ($local_status=='VALID') {
          $valid_keyIdentifiers[]="'".$db_link->escape_string($mykey['keyIdentifier'])."'";
        }
        
        
        $sql="select * from gks_paroxos_tf1_keys 
        where paroxos_id=".$paroxos_id."
        and afm='".$db_link->escape_string($myafm['afm'])."'
        and keyIdentifier='".$db_link->escape_string($mykey['keyIdentifier'])."'";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}        
        if ($result->num_rows==0) {
          $sql="insert into gks_paroxos_tf1_keys (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          local_status,paroxos_id,afm,
          keyIdentifier,keyVersion,algorithm,purpose,
          status,issuedAt,revokedAt,validFrom,validTo,
          installationVerifiedAt,revokeReason,linkBaseUrl
          ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          '".$local_status."',".$paroxos_id.",'".$db_link->escape_string($myafm['afm'])."',
          '".$db_link->escape_string($mykey['keyIdentifier'])."',
          ".intval($mykey['keyVersion']).",
          '".$db_link->escape_string($mykey['algorithm'])."',
          '".$db_link->escape_string($mykey['purpose'])."',
          '".$db_link->escape_string($mykey['status'])."',
          ".$issuedAt.",
          ".$revokedAt.",
          ".$validFrom.",
          ".$validTo.",
          ".$installationVerifiedAt.",
          '".$db_link->escape_string($mykey['revokeReason'])."',
          '".$db_link->escape_string($mykey['linkBaseUrl'])."'
          )";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}        
          
        } else {
          $row = $result->fetch_assoc();
          $id_paroxos_tf1_keys=$row['id_paroxos_tf1_keys'];
          $sql="update gks_paroxos_tf1_keys set 
          mydate_edit=now(),
          user_id_edit=".$my_wp_user_id.",
          myip='".$db_link->escape_string($gkIP)."',
          local_status='".$local_status."',
          keyVersion=".intval($mykey['keyVersion']).",
          algorithm='".$db_link->escape_string($mykey['algorithm'])."',
          purpose='".$db_link->escape_string($mykey['purpose'])."',
          status='".$db_link->escape_string($mykey['status'])."',
          issuedAt=".$issuedAt.",
          revokedAt=".$revokedAt.",
          validFrom=".$validFrom.",
          validTo=".$validTo.",
          installationVerifiedAt=".$installationVerifiedAt.",
          revokeReason='".$db_link->escape_string($mykey['revokeReason'])."',
          linkBaseUrl='".$db_link->escape_string($mykey['linkBaseUrl'])."'
          where id_paroxos_tf1_keys=".$id_paroxos_tf1_keys;
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}        
        }
      } 
      $item.='<br>'.$mycount['ISSUED'].' '.gks_lang('εκδόθηκαν');
      $item.='<br>'.$mycount['VERIFIED'].' '.gks_lang('επαληθεύθηκαν');
      $item.='<br>'.$mycount['REVOKED'].' '.gks_lang('ανακλήθηκαν');

      
      $html[]=$item;
      
      if (count($valid_keyIdentifiers)>0) {
        $sql="update gks_paroxos_tf1_keys set 
        local_status='ACTIVE' 
        where keyIdentifier in (".implode(',',$valid_keyIdentifiers).")
        and secret<>''
        order by validTo desc limit 1";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}          
      }
    }
    
    
    $return = array('success' => true, 'message' =>  base64_encode('OK'));
    $return['html']=implode('<br>',$html);
    echo json_encode($return); die();    
    
    break; }  
  case 'tf1_create_keys':
    {
    $cret=gks_paroxos_get_offline_qrcode_key_ilyda_com();
    if ($cret['success']==false) {
      $return = array('success' => false, 'message' => base64_encode($cret['message']));
      echo json_encode($return); die();}
    
    $return = array('success' => true, 'message' =>  base64_encode('OK'));
    $return['html']=$cret['html']; 
    echo json_encode($return); die();  

    
    break;}
         
  default:
  
    break;  
}

$return = array('success' => false, 'message' =>  base64_encode(gks_lang('Ανανεώστε την σελίδα')));
echo json_encode($return); die();





  