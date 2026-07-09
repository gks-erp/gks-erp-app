<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_nexi_api_get_transactions($mydaydif, $company_id, $in_transaction_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;

  //echo $mydaydif.'|'.$company_id.'|'.$in_transaction_id;die();
  
  if ($in_transaction_id=='') {
    $mydaydif=intval($mydaydif); 
    $company_id=intval($company_id); 
    if ($company_id<=0) {
      if (!$result) {debug_mail(false,'company is not set','');die('company is not set');}
    }
    $sql="select * from gks_company where id_company=".$company_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'company not found',$sql);die('company not found');
    } else {
      $row_company = $result->fetch_assoc();
      $nexi_username=trim_gks($row_company['nexi_username']);
      $nexi_password=($row_company['nexi_password']);
      $nexi_authorization_code=($row_company['nexi_authorization_code']);
      $nexi_x_api_key=($row_company['nexi_x_api_key']);
      if ($nexi_username=='') {debug_mail(false,'nexi_username is not set for company '.$company_id,$sql);die('nexi_username is not set for company '.$company_id);}
      if ($nexi_password=='') {debug_mail(false,'nexi_password is not set for company '.$company_id,$sql);die('nexi_password is not set for company '.$company_id);}
      if ($nexi_authorization_code=='') {debug_mail(false,'nexi_authorization_code is not set for company '.$company_id,$sql);die('nexi_authorization_code is not set for company '.$company_id);}
      if ($nexi_x_api_key=='') {debug_mail(false,'nexi_x_api_key is not set for company '.$company_id,$sql);die('nexi_x_api_key is not set for company '.$company_id);}
      
      //echo '<pre>ssssss';die();
    }
  } else {
    $sql="select MID from gks_nexi_transaction where Id='".$db_link->escape_string($in_transaction_id)."'";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'transaction not found',$sql);die('transaction not found');
    }
    $row = $result->fetch_assoc();
    $MID=trim_gks($row['MID']);
    if ($MID=='') {
      debug_mail(false,'MID is empty',$sql);die('MID is empty');
    }
    $sql="select * from gks_company where nexi_mid like '".$db_link->escape_string($MerchantId)."'";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'company not found',$sql);die('company not found');
    } else {
      $row_company = $result->fetch_assoc();
      $nexi_username=trim_gks($row_company['nexi_username']);
      $nexi_password=($row_company['nexi_password']);
      $nexi_authorization_code=($row_company['nexi_authorization_code']);
      $nexi_x_api_key=($row_company['nexi_x_api_key']);
      if ($nexi_username=='') {debug_mail(false,'nexi_username is not set for company '.$company_id,$sql);die('nexi_username is not set for company '.$company_id);}
      if ($nexi_password=='') {debug_mail(false,'nexi_password is not set for company '.$company_id,$sql);die('nexi_password is not set for company '.$company_id);}
      if ($nexi_authorization_code=='') {debug_mail(false,'nexi_authorization_code is not set for company '.$company_id,$sql);die('nexi_authorization_code is not set for company '.$company_id);}
      if ($nexi_x_api_key=='') {debug_mail(false,'nexi_x_api_key is not set for company '.$company_id,$sql);die('nexi_x_api_key is not set for company '.$company_id);}
    }
  }
  
  
  
  
  $mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
  $time_vardia=_time_user($mytimenow, 1);
  
  $time_vardia-= GKS_ERP_START_VARDIA*60*60;
  $today_vardia = date('Y-m-d',$time_vardia);
  $today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
  $today_vardia = _time_user($today_vardia, -1);
  $today_vardia_time = $today_vardia;
  $today_vardia_min = date('Y-m-d\TH:i:s\Z', $today_vardia);
  $today_vardia_max = date('Y-m-d\TH:i:s\Z', $today_vardia+24*60*60);

  //$today_vardia_min='2024-07-01T00:00:00Z';
  
  //echo $today_vardia_min.'|'.$today_vardia_max;die();

  $ret=gks_eftpos_get_token(7,$row_company);
  //echo '<pre>token ggggggggg ... ';print_r($ret);die();
  if ($ret['success']==false) {$ret['message']=base64_encode($ret['message']);echo json_encode($ret); die();}
  $access_token=$ret['data']['access_token'];
  
  $headers = array(
    'Content-Type:application/json',
    'Authorization: Bearer '. $access_token,
    'X-Api-Key: '.$nexi_x_api_key,
  );

  $url=GKS_NEXI_COM_API.'/api/v2.1/transactionintent/?Timestamp_min='.$today_vardia_min.'&Timestamp_max='.$today_vardia_max;
  //echo '<pre>'.$url;die();
  
  
  $mydata=array();
  
  do {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $return = curl_exec($ch);
    curl_close($ch);
    //echo 'ret:'.$return."\n";die();
    $mydata_part=json_decode($return,true);
    //echo '<pre>';print_r($mydata_part);die();
    
    foreach ($mydata_part['results'] as $part) {
      $mydata[]=array(
        'intentId'=>trim_gks($part['Id']),
        'id_nexi_transaction'=>0,
        'id_eftpos_transaction'=>0,
        'gks_transaction' => intval($part['Transaction']),
        'data'=>$part,
        'gks_trans' => array(),
      );
      
    }
    
    if (isset($mydata_part['next'])==false) break;
    $temp=trim_gks($mydata_part['next']);   
    if ($temp=='') break;
    $url=$temp;
    
  } while (true);    
  
  //echo '<pre>';print_r($mydata);die();
  
  
  $url=GKS_NEXI_COM_API.'/api/v2.1/transaction/?Timestamp_min='.$today_vardia_min.'&Timestamp_max='.$today_vardia_max;;
  do {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
    $return = curl_exec($ch);
    curl_close($ch);
    //echo 'ret:'.$return."\n";die();
    $mydata_part=json_decode($return,true);
    
    //echo '<pre>';print_r($mydata_part);die();
    foreach ($mydata_part['results'] as $part) {
      $myid=intval($part['Id']);
      foreach ($mydata as &$mypart) {
        if ($mypart['gks_transaction']>0 and $myid==$mypart['gks_transaction']) {
          $mypart['gks_trans'][]=array(
            'tid' => $myid,
            'data'=> $part,
          );
          break;
        }
      }
      unset($mypart);

    }
    
    if (isset($mydata_part['next'])==false) break;
    $temp=trim_gks($mydata_part['next']);   
    if ($temp=='') break;
    $url=$temp;
    
  } while (true);   
  
  //echo '<pre>';print_r($mydata);die();
  
  foreach ($mydata as &$mytra) {
    $intentId=trim_gks($mytra['intentId']);
    $sessionId=trim_gks($mytra['data']['CustomerReference']);
    if ($intentId!='') {
      $sql="select id_nexi_transaction from gks_nexi_transaction 
      where intentId='".$db_link->escape_string($intentId)."'";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows>0) { 
        $row = $result->fetch_assoc();
        $mytra['id_nexi_transaction']=$row['id_nexi_transaction'];
      }
      
      $sql="select id_eftpos_transaction from gks_eftpos_transaction 
      where payment_acquirer_with_id=3 
      and remote_id='".$db_link->escape_string($intentId)."'";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows>0) { 
        $row = $result->fetch_assoc();
        $mytra['id_eftpos_transaction']=$row['id_eftpos_transaction'];
      }
      
    } 
    if ($mytra['id_eftpos_transaction']==0 and $sessionId!='') {
      $sql="select id_eftpos_transaction 
      from gks_eftpos_transaction 
      where payment_acquirer_with_id=3 and sessionId='".$db_link->escape_string($sessionId)."'";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows>0) { 
        $row = $result->fetch_assoc();
        $mytra['id_eftpos_transaction']=$row['id_eftpos_transaction'];
      }
    }
  }
  unset($mytra);
  //echo '<pre>';print_r($mydata);die();
  
  foreach ($mydata as &$mytra) {
    $intentId=trim_gks($mytra['intentId']);
    $sql="select id_nexi_transaction from gks_nexi_transaction where ";
    if (intval($mytra['data']['Transaction'])>0) {
      $sql.=" Id=".intval($mytra['data']['Transaction']);
    } else {
      $sql.=" intentId='".$db_link->escape_string($intentId)."'";
    }
    //if ($mytra['gks_transaction']==8116275) {echo '<pre>'.$sql;die();}
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) { 
      $sql="insert into gks_nexi_transaction (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      intentId,
      eftpos_transaction_id,
      myStatus,myResult,
      xeiristis_id,
      add_from_system,myfrom,
      Id,
      TxnType,
      Amount,TipAmount,Instalments,CurrencyCode,
      CustomerReference,
      TransactionId_org
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($intentId)."',
      ".$mytra['id_eftpos_transaction'].",
      ".$mytra['data']['Status'].",
      ".$mytra['data']['Result'].",
      0,
      'cron',
      'nexi Api',
      ".intval($mytra['data']['Transaction']).",
      ".intval($mytra['data']['TxnType']).",
      ".number_format(floatval($mytra['data']['Amount'])/100,2,'.','').",
      ".number_format(floatval($mytra['data']['TipAmount'])/100,2,'.','').",
      ".intval($mytra['data']['Instalments']).",
      ".intval($mytra['data']['CurrencyCode']).",
      '".$db_link->escape_string($mytra['data']['CustomerReference'])."',
      '".$db_link->escape_string($mytra['data']['TransactionId'])."'
      )" ;
      //echo '<pre>'.$sql; die();
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      $mytra['id_nexi_transaction']=$db_link->insert_id; 
      
      if ($mytra['id_eftpos_transaction']>0) {
        $sql="update gks_eftpos_transaction set 
        xxx_transaction_id=".$mytra['id_nexi_transaction'].",
        remote_id='".$db_link->escape_string($intentId)."'
        where id_eftpos_transaction=".$mytra['id_eftpos_transaction'];
        //and xxx_transaction_id=0 and remote_id=0
        
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
        $mytra['id_nexi_transaction']=$db_link->insert_id; 
      }
    } else {
      
      $row = $result->fetch_assoc(); 
      $id_nexi_transaction=$row['id_nexi_transaction'];
      $mytra['id_nexi_transaction']=$id_nexi_transaction;
      
      $sql="update gks_nexi_transaction set ";
      if ($mytra['id_eftpos_transaction']>0) $sql.="eftpos_transaction_id=".$mytra['id_eftpos_transaction'].",";
      if (intval($mytra['data']['Transaction'])>0) $sql.="Id=".intval($mytra['data']['Transaction']).",";
      $sql.="
      myStatus=".$mytra['data']['Status'].",
      myResult=".$mytra['data']['Result'].",
      TxnType=".intval($mytra['data']['TxnType']).",
      Amount=".number_format(floatval($mytra['data']['Amount'])/100,2,'.','').",
      TipAmount=".number_format(floatval($mytra['data']['TipAmount'])/100,2,'.','').",
      Instalments=".intval($mytra['data']['Instalments']).",
      CurrencyCode=".intval($mytra['data']['CurrencyCode']).",
      CustomerReference='".$db_link->escape_string($mytra['data']['CustomerReference'])."',
      TransactionId_org='".$db_link->escape_string($mytra['data']['TransactionId'])."'

      where id_nexi_transaction=".$id_nexi_transaction;
      
      
      
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    }
  }
  unset($mytra);
  
  
  foreach ($mydata as &$mytra) {
    $intentId=trim_gks($mytra['intentId']);
    $id_nexi_transaction=$mytra['id_nexi_transaction'];
    if ($mytra['gks_transaction']>0 and $id_nexi_transaction>0 and count($mytra['gks_trans'])==1) {
      $resitem=$mytra['gks_trans'][0]['data'];
      //print '<pre>';print_r($resitem);
      
      $new_fileds=[];
      $sqlU=array();     
      $fab=array('Approved','Voided');
      $fai=array('TxnType','STAN','BatchNumber','Instalments','PosEntryMode','CurrencyCode');
      $faf=array('Amount','DccAmount','TipAmount','CashbackAmount','LoyaltyRedemptionAmount');
      $fad=array('Timestamp','VoidTimestamp');
      $fas=array('Id','ExternalId','CardPAN','CardHash','Batch','Acquirer','TID','MID',
      'Cryptogram','HostResponseCode','RRN','AuthCode','OriginalRRN','OriginalAuthCode',
      'DccCurrencyCode','CustomerReference');
      
      
      foreach ($resitem as $key => $rtr) {
        if ($rtr!==null) {
          if (in_array($key,$fab)) {
            $sqlU[]=$key."=".(boolval($rtr) ? '1':'0');
          } else if (in_array($key,$fai)) {
            $sqlU[]=$key."=".intval($rtr);
          } else if (in_array($key,$faf)) {
            $sqlU[]=$key."=".intval($rtr)/100;
          } else if (in_array($key,$fad)) {
            $sqlU[]=$key."='".date('Y-m-d H:i:s',strtotime($rtr))."'";
          } else if (in_array($key,$fas)) {
            $sqlU[]=$key."='".$db_link->escape_string($rtr)."'";
          } else {
            $new_fileds[]=$key;
          }
        }
      }
      
      //if ($id_nexi_transaction==10049) die();
      
      $sql="update gks_nexi_transaction set 
      ".implode(',',$sqlU).",
      myjson='".$db_link->escape_string(json_encode($resitem))."'";
      
      if (isset($resitem['Timestamp'])) {
        $sql.=", mydate_add='".date('Y-m-d H:i:s',strtotime($resitem['Timestamp']))."'";
      }
      $sql.=" where id_nexi_transaction=".$id_nexi_transaction;
      
      
      
      //if ($id_nexi_transaction==10024) {echo '<pre>'.$sql;die();}
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      
      if (count($new_fileds)>0)  {
        debug_mail(false,'new fields in nexi.php',print_r($new_fileds,true)."\n".print_r($resitem,true));
      }      
      
      
      
    }
    
  }
  unset($mytra);

  //echo '<pre>';print_r($mydata);die();
  
 
  return true;
}
  