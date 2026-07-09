<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_get_www1_aade_gr_tameiakes_myweb_q1_php_money($a) {
	$a=trim_gks($a);
	$a=trim_gks(str_replace('€','',$a));
	if (strpos($a,',')===false and strpos($a,'.')!==false) {  // exei . alla den exei ,
		$a=floatval($a);	
	} else if (strpos($a,',')!==false and strpos($a,'.')===false) { // exei , alla den exei .
		$a=str_replace(',','.',$a);	
		$a=floatval($a);	
	} else if (strpos($a,',')===false and strpos($a,'.')===false) { // drn exei tipota
		$a=floatval($a);
	} else { //exei kai ta . kai ,
		$pos1=strpos($a,',');
		$pos2=strpos($a,'.');
		if ($pos1>$pos2) { // p.x. 123.456,78
			$a=str_replace('.','',$a);	
			$a=str_replace(',','.',$a);	
			$a=floatval($a);
		} else {           // p.x. 123,456.78
			$a=str_replace(',','',$a);
			$a=floatval($a);	
		}
		
	}
	return $a;
}


function gks_get_www1_aade_gr_tameiakes_myweb_q1_php($url,$params=array()) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  $ret = array('success' => false, 'message' => 'generic error');	
	//echo '<pre>';print_r($params);die();
	
	$company_id=0; if (isset($params['company_id'])) $company_id=$params['company_id'];
	$company_sub_id=0; if (isset($params['company_sub_id'])) $company_sub_id=$params['company_sub_id'];
	
	
	//if (1==1) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  //'Content-Type: application/x-www-form-urlencoded',
	  //'Content-Length: ' . strlen($post_data),
	  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
	  'Referer: https://www.google.gr',
	  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	  
	));

  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close ($ch);
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);


//	echo '<pre>';
//  echo 'gks_curl_info:'."\n";
//  print_r($gks_curl_info);
//  echo "\n\n";
//  echo 'gks_curl_errno:'."\n";
//  var_dump($gks_curl_errno);
//  echo "\n\n";
//  echo 'result:'."\n";
//  var_dump($result);
//  echo "\n\n";
//	die();
	
	if ($gks_curl_http_code!=200) {
		$ret['message']=gks_lang('Δεν είναι δυνατή η πρόσβαση στο').': '.$url;
    debug_mail(false,$ret['message'],'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
		return $ret;}
	
  $parts=explode("\r\n\r\n",$result,2);
  if (count($parts)!=2) {
		$ret['message']=gks_lang('Η σελίδα δεν επέστρεψε δεδομένα').'<br>URL: '.$url;
    debug_mail(false,$ret['message'],'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return $ret;}

  $response=trim($parts[1]);
  if ($response=='') {
		$ret['message']=gks_lang('Η σελίδα δεν επέστρεψε δεδομένα').'<br>URL: '.$url;
    debug_mail(false,$ret['message'],'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return $ret;}

  
  	//file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/gg3.html',$response);
	//} else {
	//	$response=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/gg2.html');
	//}
	
	//echo $response;
	
	$dom = new DomDocument();
	@$dom->loadHTML($response);
	$tables = $dom->getElementsByTagName('table'); 
	if ($tables->length!=1) return $ret;
  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');  

	$data=[];
	for ($i = 0; $i < $mytrs->length; $i++) {
		$mydts = $mytrs[$i]->getElementsByTagName('td'); 
		//var_dump($mydts);
		if ($mydts->length==2) {
			$td1=trim_gks($mydts[0]->textContent);
			$td2=trim_gks($mydts[1]->textContent);
			if ($td1!='' and $td2!='') {
				$data[$td1]=$td2;
			}
		}
	}

	//print '<pre>';print_r($data);die();

	if (isset($data['Καθαρή αξία Α'])==false and isset($data['Καθαρή αξία Β'])==false and isset($data['Καθαρή αξία Γ'])==false) {
		$ret['message']=gks_lang('Δεν βρέθηκαν δεδομένα').'<br>'.gks_lang('Πιθανόν η απόδειξη να μην έχει σταλεί ακόμα στην ΑΑΔΕ');
	  debug_mail(false,$ret['message'],$sql); return $ret;}
		
	
	//$data['Συνολική αξία'] => € 0.00
	if (isset($data['Συνολική αξία'])) {
	  $data['Συνολική αξία']=gks_get_www1_aade_gr_tameiakes_myweb_q1_php_money($data['Συνολική αξία']);
	}
	//$data['Ποσό πληρωμής με κάρτα'] => € 0.00
	if (isset($data['Ποσό πληρωμής με κάρτα'])) {
	  $data['Ποσό πληρωμής με κάρτα']=gks_get_www1_aade_gr_tameiakes_myweb_q1_php_money($data['Ποσό πληρωμής με κάρτα']);
	}
	
	//$data['Καθαρή αξία Α']='€ 12.344,9';
	$aae=['Α','Β','Γ','Δ','Ε'];
	foreach ($aae as $aaei) {
		if (isset($data['Καθαρή αξία '.$aaei])==false) $data['Καθαρή αξία '.$aaei]=0;
		$data['Καθαρή αξία '.$aaei]=gks_get_www1_aade_gr_tameiakes_myweb_q1_php_money($data['Καθαρή αξία '.$aaei]);
		if (isset($data['ΦΠΑ '.$aaei])==false) $data['ΦΠΑ '.$aaei]=0;
		$data['ΦΠΑ '.$aaei]=gks_get_www1_aade_gr_tameiakes_myweb_q1_php_money($data['ΦΠΑ '.$aaei]);
	} 


		
	
	
	$products=array();
	$lineNumber=0;
	foreach ($aae as $aaei) {
		if ($data['Καθαρή αξία '.$aaei]!=0) {
			$lineNumber++;
			
			//https://www.taxheaven.gr/circulars/42138/a-1173-2022https://www.taxheaven.gr/circulars/42138/a-1173-2022
			
			$vatCategory=1;
			switch ($aaei) { 	
				case 'Α': 	$vatCategory=3;	break; 	//6%
				case 'Β': 	$vatCategory=2;	break; 	//13%
				case 'Γ': 	$vatCategory=1;	break; 	//24%
				case 'Δ': 	$vatCategory=0;	break; 	//36%
				case 'Ε': 	$vatCategory=7;	break; 	//0%
			}
				
				
	    $products[]=array(
	      'lineNumber' => $lineNumber,
	      'quantity' => 1,
	      'measurementUnit' => 1,
	      'netValue' => $data['Καθαρή αξία '.$aaei],
	      'vatCategory' => $vatCategory,
	      'vatAmount' => $data['ΦΠΑ '.$aaei],
	      'vatExemptionCategory' => 0,
	      
	      'withheldAmount' => 0,
	      'withheldPercentCategory' => 0,
	      'stampDutyAmount' => 0,
	      'stampDutyPercentCategory' => 0,
	      'feesAmount' => 0,
	      'feesPercentCategory' => 0,
	      'otherTaxesAmount' => 0,
	      'otherTaxesPercentCategory' => 0,
	      'deductionsAmount' => 0,
	      'lineComments' => '',
	      
	      'incomeClassification' => [],
	      'expensesClassification' => [],
	    );
		}
	}



	$invoiceType='11.1'; //ALP
	if ($data['Είδος παραστατικού']=='ΑΠΟΔΕΙΞΗ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ') {
		$invoiceType='11.2'; //APY
	}
	
	
	
	$totalNetValue=$data['Καθαρή αξία Α']+$data['Καθαρή αξία Β']+$data['Καθαρή αξία Γ']+$data['Καθαρή αξία Δ']+$data['Καθαρή αξία Ε'];
	$totalVatAmount=$data['ΦΠΑ Α']+$data['ΦΠΑ Β']+$data['ΦΠΑ Γ']+$data['ΦΠΑ Δ'];

	//print '<pre>';print_r($data);die();
	
	$paymentMethods_type=3; //Metrita
	$paymentMethods_paymentMethodInfo=gks_lang('Μετρητά');
	$paymentMethods_amount=$data['Συνολική αξία'];
	
	if (isset($data['Ποσό πληρωμής με κάρτα']) and $data['Ποσό πληρωμής με κάρτα']>0) {
  	$paymentMethods_type=7; //POS / e-POS
  	$paymentMethods_paymentMethodInfo='POS / e-POS';
  	$paymentMethods_amount=$data['Ποσό πληρωμής με κάρτα'];
  }
	
	$aade_skopos_diakinisis_code=1; //Polisi   (1)
	//$aade_skopos_diakinisis_code=0; //Ypiresia (9)
		
	$myinvoice=array(
    //'filename' => $fullpath,
    'invoiceuid' => $data['ΑΦΜ εκδότη'].'@'.$data['ΦΗΜ'].'@'.$data['Προοδευτικός α/α'].'@'.str_replace(' ','',$data['Ημερομηνία, ώρα']),
    'mark' => '',
    'afm_issuer' => $data['ΑΦΜ εκδότη'],
    'user_id_issuer' => 0,
    'afm_counterpart' => '',
    'user_id_counterpart' => 0,
    //'SenderVAT' => $SenderVAT,
    'issueDate'=> showDate(strtotime($data['Ημερομηνία, ώρα']),'Y-m-d H:i:s',-1),
    'issueDateint'=> _time_user(strtotime($data['Ημερομηνία, ώρα']),0),
    'id_acc_inv' => 0,
    'text'=> '',
    'invoiceType' => $invoiceType,
    'inv_type_descr' => '',
    'seira' => $data['ΦΗΜ'],
    'number' => intval($data['Προοδευτικός α/α']),
    
    'currency' => 'EUR',
    'vehicleNumber' => '',
    'gks_price_net' => $totalNetValue,
    'gks_price_fpa' => $totalVatAmount,
    'gks_price_netfpa' => $totalNetValue + $totalVatAmount,
    'totalWithheldAmount' => 0,
    'totalFeesAmount' => 0,
    'totalStampDutyamount' => 0,
    'totalOtherTaxesAmount' => 0,
    'totalDeductionsAmount' => 0,
    'gks_price_total' => $totalNetValue + $totalVatAmount,
    
    'paymentMethods' => array(array(
      'type'=>$paymentMethods_type,
      'amount'=>$paymentMethods_amount, //$totalNetValue + $totalVatAmount,
      'paymentMethodInfo' => $paymentMethods_paymentMethodInfo,
      'payment_acquirer_id'=>0,
    )),
    

    'aade_skopos_diakinisis_code' => $aade_skopos_diakinisis_code,
    
    'products' => $products,
  );
  

	

	$sql="SELECT id_acc_inv, aade_invoicemark,inv_state,aade_invoiceuid
	FROM gks_acc_inv 
	WHERE aade_invoiceuid like '".$db_link->escape_string($myinvoice['invoiceuid'])."'
	and gks_acc_inv.company_id=".$company_id;
	$result = $db_link->query($sql);  
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	if ($result->num_rows>=1) {
	  $row = $result->fetch_assoc();
    $tmpmsg=gks_lang('Υπάρχει ήδη στο σύστημα παραστατικό με την ίδια ταυτότητα <b>[1]</b><br>για αυτήν την εταιρεία<br>και είναι το: <a href="admin-acc-inv-item.php?id=[2]" class="gks_link">#[2]</a> <span class="acc_inv_state_[3]">[4]</span>');
    $tmpmsg=str_replace('[1]',$myinvoice['invoiceuid'],$tmpmsg);
    $tmpmsg=str_replace('[2]',$row['id_acc_inv'],$tmpmsg);
    $tmpmsg=str_replace('[3]',$myinvoice['invoiceuid'],$tmpmsg);
    $tmpmsg=str_replace('[4]',getAccInvStateDescr($row['inv_state']),$tmpmsg);
	  
	  $ret['message']=$tmpmsg;
	  debug_mail(false,'exist lianikis',$ret['message'].'<br>'.$sql); return $ret;}
	  

	$sql="select * from gks_acc_eidi_parastatikon where eidos_parastatikou_aade_code='".$db_link->escape_string($myinvoice['invoiceType'])."'";
	$result = $db_link->query($sql);  
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	if ($result->num_rows==0) {
	  $tmpmsg=gks_lang('Δεν βρέθηκε ο τύπος παραστατικού <b>[1]</b> στο σύστημα');
	  $tmpmsg=str_replace('[1]',$myinvoice['invoiceType'],$tmpmsg);
	  $tmpmsg.='<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος');
	  $ret['message']=$tmpmsg;
	  debug_mail(false,'type not found',$ret['message'].'<br>'.$sql); return $ret;}
	$row_parast = $result->fetch_assoc();
	//echo '<pre>';print_r($row_parast);die();
	//echo '<pre>';print_r($myinvoice);die();
  
  $myinvoice['from_aade_import']='apo_allon';
	  
	if ($company_id>0) {
	  $sql="SELECT id_company, company_title, company_eponimia, company_afm FROM gks_company WHERE id_company=".$company_id;
	  $result = $db_link->query($sql);  
	  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  if ($result->num_rows>=1) {
	    $row = $result->fetch_assoc();
	    $myinvoice['company_id']=$row['id_company'];
	    $myinvoice['company_sub_id']=0;
	    $myinvoice['company_title']=$row['company_title'];
	    $myinvoice['company_eponimia']=$row['company_eponimia'];
	    $myinvoice['company_afm']=$row['company_afm'];
	  }
	} else if ($company_sub_id>0) {
	  $sql="SELECT id_company, id_company_sub,
	  company_sub_title, company_sub_eponimia, company_afm 
		FROM gks_company_subs 
		LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
	  WHERE id_company_sub=".$company_id;
	  $result = $db_link->query($sql);  
	  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  if ($result->num_rows>=1) {
	    $row = $result->fetch_assoc();
	    $myinvoice['company_id']=0;
	    $myinvoice['company_sub_id']=$row['id_company_sub'];
	    $myinvoice['company_title']=$row['company_sub_title'];
	    $myinvoice['company_eponimia']=$row['company_sub_eponimia'];
	    $myinvoice['company_afm']=$row['company_afm'];
	  }		
	}

	if ($myinvoice['company_id']==0 and $myinvoice['company_sub_id']) {
	  $row = $result->fetch_assoc();
	  $ret['message']=gks_lang('Δεν βρέθηκε η σχετική εταιρεία-υποκατάστημα');
	  debug_mail(false,$ret['message'],$sql); return $ret;}
		
		
	$myinvoice['user_id']=0;
	$myinvoice['user_afm']=$myinvoice['afm_issuer'];
	$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, gks_users.eponimia, gks_users.title, gks_users.afm,gks_users.doy,gks_users.epaggelma,
	gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk,gks_users.ma_country_id, gks_users.ma_nomos_id, 
	
	table_last_name.mylast_name, table_first_name.myfirst_name,table_mobile.mymoobile,gks_users.phone_home,".GKS_WP_TABLE_PREFIX."users.user_email,
	".GKS_WP_TABLE_PREFIX."users.fiscal_position_id,".GKS_WP_TABLE_PREFIX."users.pricelist_id
	
	FROM (((gks_users 
	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
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
	LEFT JOIN (
	  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
	  FROM ".GKS_WP_TABLE_PREFIX."usermeta
	  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
	)  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id
	
	WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm like '".$db_link->escape_string($myinvoice['user_afm'])."'
	order by ".GKS_WP_TABLE_PREFIX."users.ID";
	$result = $db_link->query($sql);  
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  
	if ($result->num_rows>=1) {
	  $row = $result->fetch_assoc();
	  $myinvoice['user_id']=$row['ID'];
	  $myinvoice['eponimia']=trim_gks($row['eponimia']);
	  $myinvoice['title']=trim_gks($row['title']);
	  $myinvoice['afm']=trim_gks($row['afm']);
	  $myinvoice['doy']=trim_gks($row['doy']);
	  $myinvoice['epaggelma']=trim_gks($row['epaggelma']);
	  $myinvoice['user_first_name']=trim_gks($row['myfirst_name']);
	  $myinvoice['user_last_name']=trim_gks($row['mylast_name']);
	  $myinvoice['user_mobile']=trim_gks($row['mymoobile']);
	  $myinvoice['user_email']=trim_gks($row['user_email']);
	  $myinvoice['ma_odos']=trim_gks($row['ma_odos']);
	  $myinvoice['ma_arithmos']=trim_gks($row['ma_arithmos']);
	  $myinvoice['ma_orofos']=trim_gks($row['ma_orofos']);
	  $myinvoice['ma_perioxi']=trim_gks($row['ma_perioxi']);
	  $myinvoice['ma_poli']=trim_gks($row['ma_poli']);
	  $myinvoice['ma_tk']=trim_gks($row['ma_tk']);
	  $myinvoice['ma_country_id']=trim_gks($row['ma_country_id']);
	  $myinvoice['ma_nomos_id']=trim_gks($row['ma_nomos_id']);
	  $myinvoice['fiscal_position_id']=trim_gks($row['fiscal_position_id']);
	  $myinvoice['pricelist_id']=trim_gks($row['pricelist_id']);
	
	}

	if ($myinvoice['user_id']==0) {
    $tmpmsg=gks_lang('Στις επαφές σας δεν βρέθηκε ο αντισυμβαλλόμενος με ΑΦΜ <b>[1]</b>');
    $tmpmsg.='<br>'.gks_lang('Δημιουργήστε πρώτα την επαφή χρησιμοποιώντας το εικονίδιο');
    $tmpmsg.=' <a href="admin-users-item.php?id=-1#createfromafm=[1]|sup" target="_blank"><i class="fas fa-save user_create" data-val="'.$myinvoice['user_afm'].'" data-cus_sup="sup" title="'.gks_lang('Δημιουργία επαφής').'"></i></a> '.gks_lang('και δοκιμάστε ξανά');
    $tmpmsg=str_replace('[1]',$myinvoice['user_afm'],$tmpmsg);

		$ret['message']=$tmpmsg;

		
	  debug_mail(false,$ret['message'],$sql); return $ret;}
	
	$myinvoice['aade_user_id']=$my_wp_user_id;
	
	$myinvoice['tropos_pliromis']=1;
	if ($myinvoice['paymentMethods'][0]['type']>0) {
	
	  
	  $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
	  WHERE aade_tropos_pliromis_id=".$myinvoice['paymentMethods'][0]['type']." 
	  AND payment_acquirer_name Like '".$db_link->escape_string($myinvoice['paymentMethods'][0]['paymentMethodInfo'])."'
	  and payment_acquirer_disabled=0
	  order by id_payment_acquirer";
	  $result = $db_link->query($sql);  
	  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;} 
	  if ($result->num_rows>=1) {
	    $row = $result->fetch_assoc();
	    $myinvoice['tropos_pliromis']=$row['id_payment_acquirer'];
	  }
	  
	  if ($myinvoice['tropos_pliromis']==1) {
	    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
	    WHERE aade_tropos_pliromis_id=".$myinvoice['paymentMethods'][0]['type']." 
	    AND payment_acquirer_name Like '%".$db_link->escape_string($myinvoice['paymentMethods'][0]['paymentMethodInfo'])."%'
	    and payment_acquirer_disabled=0
	    order by id_payment_acquirer";
	    $result = $db_link->query($sql);  
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	    if ($result->num_rows>=1) {
	      $row = $result->fetch_assoc();
	      $myinvoice['tropos_pliromis']=$row['id_payment_acquirer'];
	    }    
	  }
	  if ($myinvoice['tropos_pliromis']==1) {
	    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
	    WHERE aade_tropos_pliromis_id=".$myinvoice['paymentMethods'][0]['type']." 
	    and payment_acquirer_disabled=0
	    order by id_payment_acquirer";
	    $result = $db_link->query($sql);  
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	    if ($result->num_rows>=1) {
	      $row = $result->fetch_assoc();
	      $myinvoice['tropos_pliromis']=$row['id_payment_acquirer'];
	    }    
	  }
	  if ($myinvoice['tropos_pliromis']==1) {
	    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
	    WHERE aade_tropos_pliromis_id=".$myinvoice['paymentMethods'][0]['type']." 
	    AND payment_acquirer_name Like '%".$db_link->escape_string($myinvoice['paymentMethods'][0]['paymentMethodInfo'])."%'
	    order by id_payment_acquirer";
	    $result = $db_link->query($sql);  
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	    if ($result->num_rows>=1) {
	      $row = $result->fetch_assoc();
	      $myinvoice['tropos_pliromis']=$row['id_payment_acquirer'];
	    }    
	  }
	}
	
	if ($myinvoice['tropos_pliromis']<=1 and 
	    isset($gks_user_settings['gks_acc_inv']['tropos_pliromis']) and 
	    $gks_user_settings['gks_acc_inv']['tropos_pliromis']>1) {
	  $myinvoice['tropos_pliromis']=$gks_user_settings['gks_acc_inv']['tropos_pliromis'];
	  
	}

	$myinvoice['aade_skopos_diakinisis_id']=0;
	if ($myinvoice['aade_skopos_diakinisis_code']>0) {
	  $sql="select id_aade_skopos_diakinisis from gks_aade_skopos_diakinisis where aade_skopos_diakinisis_code=".$myinvoice['aade_skopos_diakinisis_code'];
	  $result = $db_link->query($sql);  
	  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;} 
	  if ($result->num_rows>=1) {
	    $row = $result->fetch_assoc();
	    $myinvoice['aade_skopos_diakinisis_id']=$row['id_aade_skopos_diakinisis'];
	  }
	}
	

	$sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code, 
	gks_acc_journal.acc_eidos_parastatikou_whi_id,
	whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros AS whi_eidos_parastatikou_stock_pros, 
	whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id AS whi_eidos_parastatikou_type_id
	FROM ((gks_acc_journal 
	LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
	LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
	LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
	WHERE gks_acc_journal.company_id=".$company_id."
	AND gks_acc_journal.company_sub_id=".$company_sub_id."
	AND gks_acc_journal.is_disable=0
	AND gks_acc_seires.is_disable=0
	AND gks_acc_seires.is_xeirografi=0";
	$sql.=" AND gks_acc_eidi_parastatikon.import_apo_allon like '%[".$db_link->escape_string($invoiceType)."]%'";

	//gks_acc_journal.acc_eidos_parastatikou_id=502
	
	$result = $db_link->query($sql);
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	if ($result->num_rows==0) {
	  
	  $sql2="select company_title from gks_company where id_company=".$company_id;
	  $result2 = $db_link->query($sql2);
	  if (!$result2) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql2); return $ret;}
	  if ($result2->num_rows==0) {
		  $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID <br><b>[1]</b><br>και όνομα<br><b>[2]</b>');
      $ret['message']=str_replace('[1]',$company_id,$ret['message']);
      $ret['message']=str_replace('[2]',$myinvoice['company_title'],$ret['message']);		  
		  
		  debug_mail(false,$ret['message'],$sql2); return $ret;}

	  $row2 = $result2->fetch_assoc();
	  $company_title_out=$row2['company_title'];
	  
	  if ($company_sub_id==0) {
	    $company_title_out.=' \ '.gks_lang('Κεντρικό');
	  } else {
	    $sql2="select company_sub_title from gks_company_subs where id_company_sub=".$company_sub_id;
	    $result2 = $db_link->query($sql2);
	    if (!$result2) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql2); return $ret;}
	    if ($result2->num_rows==0) {
			  $ret['message']=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID <br><b>[1]</b><br>της εταιρείας<br><b>[2]</b>');
        $ret['message']=str_replace('[1]',$company_sub_id,$ret['message']);
        $ret['message']=str_replace('[2]',$company_title_out,$ret['message']);		  
			  debug_mail(false,$ret['message'],$sql); return $ret;}
	    $row2 = $result2->fetch_assoc();
	    $company_title_out.=' \ '.$row2['company_sub_title'];
	  }
	  
	  $sql2="select eidos_parastatikou_descr from gks_acc_eidi_parastatikon where eidos_parastatikou_aade_code='".$db_link->escape_string($invoiceType)."'";
	  $result2 = $db_link->query($sql2);
	  if (!$result2) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql2); return $ret;}
	  if ($result2->num_rows==0) {
			$ret['message']=gks_lang('Δεν βρέθηκε ο τύπος παραστατικών με κωδικό<br><b>[1]</b><br>στο σύστημα');
			$ret['message']=str_replace('[1]',$invoiceType,$ret['message']);
			$ret['message'].='<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος');
			
			debug_mail(false,$ret['message'],$sql2); return $ret;}

	  $row2 = $result2->fetch_assoc();
	  $eidos_parastatikou_descr_out=$row2['eidos_parastatikou_descr'].' ('.gks_lang('κωδικός').': '.$invoiceType.')';
	  

    $mesage=gks_lang('Δεν βρέθηκε ενεργό ημερολόγιο που να μπορεί να δεχθεί παραστατικά τύπου<br><b>[1]</b><br>και αντίστοιχη ενεργή σειρά για την εταιρεία<br><b>[2]</b>');
    $mesage=str_replace('[1]',$eidos_parastatikou_descr_out,$mesage);
    $mesage=str_replace('[2]',$company_title_out,$mesage);
    
    $sql2="select eidos_parastatikou_descr,eidos_parastatikou_aade_code from gks_acc_eidi_parastatikon where import_apo_allon like '%[".$db_link->escape_string($invoiceType)."]%'";
    $result2 = $db_link->query($sql2);
    if (!$result2) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql2); return $ret;}
    $eidos_parastatikou_descr_array=array();
    while ($row2 = $result2->fetch_assoc()) {
      $eidos_parastatikou_descr_array[]='<b>'.$row2['eidos_parastatikou_descr'].'</b>';
    }
    
    if (count($eidos_parastatikou_descr_array)==0) {
      $mesage.='<br><br>'.gks_lang('Δεν βρέθηκαν σχετικοί τύποι παραστατικών').'<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος');
    } else {
      $mesage.='<br><br>'.gks_lang('Τύποι παραστατικών που μπορούν να δεχθούν τον παραπάνω τύπο είναι').':<br>'.implode('<br>',$eidos_parastatikou_descr_array);
    }
    $ret['message']=$mesage;
    debug_mail(false,$ret['message'],$sql2); return $ret;

	  
	}
	  
	$row = $result->fetch_assoc();
	$myinvoice['inv_acc_journal_id']=$row['id_acc_journal'];
	$myinvoice['inv_acc_seira_id']=$row['id_acc_seira'];
	$myinvoice['inv_acc_seira_code']=$row['seira_code'];
	$myinvoice['acc_eidos_parastatikou_whi_id']=$row['acc_eidos_parastatikou_whi_id'];
	$myinvoice['whi_eidos_parastatikou_stock_pros']=$row['whi_eidos_parastatikou_stock_pros'];
	$myinvoice['whi_eidos_parastatikou_type_id']=$row['whi_eidos_parastatikou_type_id'];
	
	$whi_eidos_parastatikou_stock_pros_org=$row['whi_eidos_parastatikou_stock_pros'];
	$whi_eidos_parastatikou_type_id_org=$row['whi_eidos_parastatikou_type_id'];
	
	$warehouses_id_from=0;
	$warehouses_id_to=0;
	
	$warehouses_id_from_is_virtual=false;
	$warehouses_id_to_is_virtual=false;
	if ($whi_eidos_parastatikou_type_id_org==null) $whi_eidos_parastatikou_type_id_org=0;
	
	if ($whi_eidos_parastatikou_type_id_org==0) {
	  $warehouses_id_from=0;
	  $warehouses_id_to=0;
	  //echo 'hhh ';var_dump($whi_eidos_parastatikou_type_id_org);die();
	} else {
	  $warehouses_id_def=0;
	  if ($myinvoice['acc_eidos_parastatikou_whi_id']!=0) {
	    $sql="SELECT id_warehouse FROM gks_warehouses 
	    WHERE company_id=".$company_id." 
	    AND company_sub_id=".$company_sub_id."
	    and warehouse_disable=0
	    ORDER BY warehouse_sortorder limit 1";
	    $result = $db_link->query($sql);  
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	    if ($result->num_rows>0) {
	      $row = $result->fetch_assoc();
	      $warehouses_id_def=$row['id_warehouse'];
	    }
	  }
	    
	  if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
	    $warehouses_id_from=0;  
	    $warehouses_id_to=$warehouses_id_def;  
	    
	  } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
	    $warehouses_id_from=$warehouses_id_def;  
	    $warehouses_id_to=$warehouses_id_def;  
	    
	  } else {
	    if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
	      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
	        $warehouses_id_from=1; //virtual warehouse pelates
	        $warehouses_id_from_is_virtual=true;
	        $warehouses_id_to=$warehouses_id_def; 
	      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
	        $warehouses_id_from=2; //virtual warehouse promitheutes
	        $warehouses_id_from_is_virtual=true;
	        $warehouses_id_to=$warehouses_id_def; 
	      }
	    } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
	      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
	        $warehouses_id_to=1; //virtual warehouse pelates
	        $warehouses_id_to_is_virtual=true;
	        $warehouses_id_from=$warehouses_id_def; 
	      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
	        $warehouses_id_to=2; //virtual warehouse promitheutes
	        $warehouses_id_to_is_virtual=true;
	        $warehouses_id_from=$warehouses_id_def; 
	      }
	    }
	  }
	}
	$myinvoice['warehouses_id_from']=intval($warehouses_id_from);
	$myinvoice['warehouses_id_to']=intval($warehouses_id_to);

	$tropos_apostolis=1;
	$products_need_apostoli=0;
	if ($myinvoice['warehouses_id_from']>0 or $myinvoice['warehouses_id_to']>0) {
	  $tropos_apostolis=2;
	  $products_need_apostoli=1;
	  if (isset($gks_user_settings['gks_acc_inv']['tropos_apostolis']) and 
	      $gks_user_settings['gks_acc_inv']['tropos_apostolis']>1) {
	    $tropos_apostolis=$gks_user_settings['gks_acc_inv']['tropos_apostolis'];
	    $products_need_apostoli=1;
	  }
	}


	$inv_guid=guid_for_acc_inv();
	$sql="insert into gks_acc_inv (
	user_id_add,user_id_edit,mydate_add,mydate_edit,myip,inv_guid
	) values (
	".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
	'".$db_link->escape_string($inv_guid)."')";
	$result = $db_link->query($sql);
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	 
	$id = $db_link->insert_id;
	$myinvoice['id']=$id;

	$sxolio=gks_lang('Προσθήκη από backend, εισαγωγή από ΑΑΔΕ').' '; 
	$sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
	".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
	$result = $db_link->query($sql);        
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
  
	$set_filename='';
	$save_dir = GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/';
	if (file_exists($save_dir) == false) {
	  if (@mkdir($save_dir , 0777, true) == false ) {
	    $errors[]=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); 
	    debug_mail(false,$errors[count($errors)-1]); 
	  } else {
	    $set_filename='lianiki_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999).'-import.html';
	    $full_path=$save_dir.$set_filename;
	    $response_tofile=str_replace('<head>','<head>'."\n".'<base href="https://www1.aade.gr/tameiakes/myweb/" target="_blank">'."\n",$response);
	    
	    file_put_contents($full_path, $response_tofile);  
	  }
	}
  
	$sql="insert into gks_acc_inv_links (
	acc_inv_id, url, mydate,user_id, ip 
	) values (
	".$id.",'".$db_link->escape_string($url)."',now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
	$result = $db_link->query($sql);        
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
  


	$from_aade_import_json=array(
	
	//  'gks_price_original_net'=>$myinvoice['gks_price_net'],
	  'gks_price_net'=>$myinvoice['gks_price_net'],
	  'gks_price_fpa'=>$myinvoice['gks_price_fpa'],
	  'gks_price_netfpa'=>$myinvoice['gks_price_netfpa'],
	  'totalWithheldAmount'=>$myinvoice['totalWithheldAmount'],
	  'totalFeesAmount'=>$myinvoice['totalFeesAmount'],
	  'totalStampDutyamount'=>$myinvoice['totalStampDutyamount'],
	  'totalOtherTaxesAmount'=>$myinvoice['totalOtherTaxesAmount'],
	  'totalDeductionsAmount'=>$myinvoice['totalDeductionsAmount'],
	  'gks_price_total'=>$myinvoice['gks_price_total'],
    'paymentMethods' => $myinvoice['paymentMethods'],
     
	);
	
	
	$from_aade_import_json=json_encode($from_aade_import_json);
	
	$sql="update gks_acc_inv set
	from_aade_import='".$myinvoice['from_aade_import']."',
	from_aade_import_json='".$db_link->escape_string($from_aade_import_json)."',
	
	import_inv_acc_seira_code='".$db_link->escape_string($myinvoice['seira'])."',
	import_inv_acc_number_str='".$db_link->escape_string($myinvoice['number'])."',
	import_eidos_parastatikou_aade_code='".$db_link->escape_string($myinvoice['invoiceType'])."',
	inv_state='010draft',
	company_id=".$company_id.",
	company_sub_id=".$company_sub_id.",
	inv_acc_journal_id=".$myinvoice['inv_acc_journal_id'].",
	inv_acc_seira_id=".$myinvoice['inv_acc_seira_id'].",
	inv_acc_seira_code='".$db_link->escape_string($myinvoice['inv_acc_seira_code'])."',
	inv_date='".showDate($myinvoice['issueDateint'],'Y-m-d H:i:s',-1)."',
	user_id=".$myinvoice['user_id'].",
	eponimia='".$db_link->escape_string($myinvoice['eponimia'])."',
	title='".$db_link->escape_string($myinvoice['title'])."',
	afm='".$db_link->escape_string($myinvoice['afm'])."',
	doy='".$db_link->escape_string($myinvoice['doy'])."',
	epaggelma='".$db_link->escape_string($myinvoice['epaggelma'])."',
	user_first_name='".$db_link->escape_string($myinvoice['user_first_name'])."',
	user_last_name='".$db_link->escape_string($myinvoice['user_last_name'])."',
	user_mobile='".$db_link->escape_string($myinvoice['user_mobile'])."',
	user_lang='el-GR',
	user_email='".$db_link->escape_string($myinvoice['user_email'])."',
	ma_odos='".$db_link->escape_string($myinvoice['ma_odos'])."',
	ma_arithmos='".$db_link->escape_string($myinvoice['ma_arithmos'])."',
	ma_orofos='".$db_link->escape_string($myinvoice['ma_orofos'])."',
	ma_perioxi='".$db_link->escape_string($myinvoice['ma_perioxi'])."',
	ma_poli='".$db_link->escape_string($myinvoice['ma_poli'])."',
	ma_tk='".$db_link->escape_string($myinvoice['ma_tk'])."',
	ma_country_id=".intval($myinvoice['ma_country_id']).",
	ma_nomos_id=".intval($myinvoice['ma_nomos_id']).",
	address_extra=-1,
	fiscal_position_id=".intval($myinvoice['fiscal_position_id']).",
	pricelist_id=".intval($myinvoice['pricelist_id']).",
	aade_skopos_diakinisis_id=".$myinvoice['aade_skopos_diakinisis_id'].",
	vehicle_number='".$db_link->escape_string($myinvoice['vehicleNumber'])."',
	tropos_pliromis=".$myinvoice['tropos_pliromis'].",
	products_need_pliromi=".($myinvoice['tropos_pliromis']>=2 ? '1' : '0').",
	
	tropos_apostolis=".$tropos_apostolis.",
	products_need_apostoli=".$products_need_apostoli.",
	
	
	gks_price_original_net=".number_format($myinvoice['gks_price_net'],2,'.','').",
	gks_price_net=".number_format($myinvoice['gks_price_net'],2,'.','').",
	gks_price_fpa=".number_format($myinvoice['gks_price_fpa'],2,'.','').",
	gks_price_netfpa=".number_format($myinvoice['gks_price_netfpa'],2,'.','').",
	totalWithheldAmount=".number_format($myinvoice['totalWithheldAmount'],2,'.','').",
	totalFeesAmount=".number_format($myinvoice['totalFeesAmount'],2,'.','').",
	totalStampDutyamount=".number_format($myinvoice['totalStampDutyamount'],2,'.','').",
	totalOtherTaxesAmount=".number_format($myinvoice['totalOtherTaxesAmount'],2,'.','').",
	totalDeductionsAmount=".number_format($myinvoice['totalDeductionsAmount'],2,'.','').",
	gks_price_total=".number_format($myinvoice['gks_price_total'],2,'.','').",
	
	warehouses_id_from=".$myinvoice['warehouses_id_from'].",
	warehouses_id_to=".$myinvoice['warehouses_id_to'].",
	affect_balance=0,
	affect_balance_all_poso=1,
	affect_balance_all_poso_type='pliroteo',
	affect_balance_poso=0,
	affect_balance_pros=0,
	
	
	
	
	aade_invoiceuid='".$db_link->escape_string($myinvoice['invoiceuid'])."',
	aade_invoicemark='".$db_link->escape_string($myinvoice['mark'])."',
	aade_statuscode='Success',
	aade_send_date='".showDate($myinvoice['issueDateint'],'Y-m-d H:i:s',-1)."',
	aade_user_id=".$myinvoice['aade_user_id'].",
	aade_xml_response='".$db_link->escape_string($set_filename)."'
	where id_acc_inv=".$id." limit 1";
	$result = $db_link->query($sql);        
	if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  	
  gks_aade_update_mark_from_id(['mark'=>$myinvoice['mark'],'acc_inv_id'=>$id]);


	foreach ($myinvoice['products'] as $product) {
	
	  if ($product['quantity']==0) $product['quantity']=1;
	  
	  $product_monada_id=0;
	  if ($product['measurementUnit']==1) $product_monada_id=1; //temaxia
	  else if ($product['measurementUnit']==2) $product_monada_id=11;  //kila
	  else if ($product['measurementUnit']==3) $product_monada_id=44;  //litra
	  
	  $product_fpa_base_id=0;
	  $product_fpa_id=0;
	  $product_fpa_pososto=0;
	  
	  $fiscal_position_id=1; //Lianikis esoterikou
	  if ($row_parast['eidos_parastatikou_need_afm']==1) $fiscal_position_id=11; //Xondrikis esoterikou
	  
	  
	  if ($product['vatCategory']>0) {
	    $sql="select * from gks_aade_katigoria_fpa where aade_katigoria_fpa_code=".$product['vatCategory'];
	    $result = $db_link->query($sql);        
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	    if ($result->num_rows>=1) {
	      $row = $result->fetch_assoc();
	      $product_fpa_base_id=$row['fpa_base_id'];
	      $product_fpa_pososto=$row['aade_katigoria_fpa_pososto'];
	      $product_fpa_id=$row['direct_fpa_id'];

	    }
	  }
	  
	  
	  $sql="insert into gks_acc_inv_products (
	  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
	  acc_inv_id,product_aa,product_id,product_descr,
	  product_monada_id_org,product_monada_id,
	  product_quantity,
	  
	  product_fpa_base_id,
	  product_fpa_id,
	  product_fpa_pososto,
	  
	  
	  product_price_include_vat,
	  product_price_start_peritem_db,
	  product_price_start_peritem_net,
	  product_price_start_peritem_fpa,
	  product_price_start_peritem_total,
	  
	  product_price_start_all_net,
	  product_price_start_all_fpa,
	  product_price_start_all_total,
	  product_price_final_peritem_db,
	  product_price_final_peritem_net,
	  product_price_final_peritem_fpa,
	  product_price_final_peritem_total,
	  product_price_final_all_net,
	  product_price_final_all_fpa,
	  product_price_final_all_total,
	  
	  product_withheldAmount,
	  product_withheldPercentCategory,
	  product_stampDutyAmount,
	  product_stampDutyPercentCategory,
	  product_feesAmount,
	  product_feesPercentCategory,
	  product_otherTaxesAmount,
	  product_otherTaxesPercentCategory,
	  product_deductionsAmount,
	  
	  product_fpa_ejeresi_id,
	  product_comments,
	  aade_lineComments,
	  from_aade_import_lock,
	  p_warehouses_id_from,
	  p_warehouses_id_to
	  
	  ) values (
	  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
	  ".$id.",".$product['lineNumber'].",2,'".$db_link->escape_string(gks_lang('Είδος με αα').' '.$product['lineNumber'])."',
	  ".$product_monada_id.",".$product_monada_id.",
	  ".$product['quantity'].",
	  
	  ".$product_fpa_base_id.",
	  ".$product_fpa_id.",
	  ".number_format($product_fpa_pososto,2,'.','').",
	
	  
	  0,
	  ".number_format($product['netValue']/$product['quantity'],2,'.','').",
	  ".number_format($product['netValue']/$product['quantity'],2,'.','').",
	  ".number_format($product['vatAmount']/$product['quantity'],2,'.','').",
	  ".number_format(($product['netValue']+$product['vatAmount'])/$product['quantity'],2,'.','').",
	  
	  ".number_format($product['netValue'],2,'.','').",
	  ".number_format($product['vatAmount'],2,'.','').",
	  ".number_format($product['netValue']+$product['vatAmount'],2,'.','').",
	  ".number_format(($product['netValue'])/$product['quantity'],4,'.','').",
	  ".number_format(($product['netValue'])/$product['quantity'],4,'.','').",
	  ".number_format(($product['vatAmount'])/$product['quantity'],4,'.','').",
	  ".number_format(($product['netValue']+$product['vatAmount'])/$product['quantity'],4,'.','').",
	  ".number_format($product['netValue'],2,'.','').",
	  ".number_format($product['vatAmount'],2,'.','').",
	  ".number_format($product['netValue']+$product['vatAmount'],2,'.','').",
	  
	  ".number_format($product['withheldAmount'],2,'.','').",
	  ".$product['withheldPercentCategory'].",
	  ".number_format($product['stampDutyAmount'],2,'.','').",
	  ".$product['stampDutyPercentCategory'].",
	  ".number_format($product['feesAmount'],2,'.','').",
	  ".$product['feesPercentCategory'].",
	  ".number_format($product['otherTaxesAmount'],2,'.','').",
	  ".$product['otherTaxesPercentCategory'].",
	  ".number_format($product['deductionsAmount'],2,'.','').",
	  
	  ".intval($product['vatExemptionCategory']).",
	  '".$db_link->escape_string($product['lineComments'])."',
	  '".$db_link->escape_string($product['lineComments'])."',
	  1,
	  ".$myinvoice['warehouses_id_from'].",
	  ".$myinvoice['warehouses_id_to']."
	  )";
	  $result = $db_link->query($sql);        
	  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  
	  $id_acc_inv_product = $db_link->insert_id;
	
	  foreach ($product['incomeClassification'] as $xarak) {
	  
	    $aade_katigoria_xarakt_esodon_id=0;
	    $aade_typos_xarakt_esodon_id=0;
	    $acc_inv_product_income_ammount=$xarak['amount'];
	    if ($xarak['classificationCategory']!='') {
	      $sql="select id_aade_katigoria_xarakt_esodon from gks_aade_katigoria_xarakt_esodon 
	      where aade_katigoria_xarakt_esodon_code like '".$db_link->escape_string($xarak['classificationCategory'])."'";
	      $result = $db_link->query($sql);        
	      if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;} 
	      if ($result->num_rows>=1) {
	        $row = $result->fetch_assoc();      
	        $aade_katigoria_xarakt_esodon_id=$row['id_aade_katigoria_xarakt_esodon'];
	      }
	    }
	    if ($xarak['classificationType']!='') {
	      $sql="select id_aade_typos_xarakt_esodon from gks_aade_typos_xarakt_esodon 
	      where aade_typos_xarakt_esodon_code like '".$db_link->escape_string($xarak['classificationType'])."'";
	      $result = $db_link->query($sql);        
	      if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	      if ($result->num_rows>=1) {
	        $row = $result->fetch_assoc();      
	        $aade_typos_xarakt_esodon_id=$row['id_aade_typos_xarakt_esodon'];
	      }
	    }
	    
	    $sql="insert into gks_acc_inv_products_income (
	    acc_inv_product_id,aade_katigoria_xarakt_esodon_id,aade_typos_xarakt_esodon_id,acc_inv_product_income_ammount
	    ) values (
	    ".$id_acc_inv_product.",
	    ".$aade_katigoria_xarakt_esodon_id.",
	    ".$aade_typos_xarakt_esodon_id.",
	    ".number_format($acc_inv_product_income_ammount, 4,'.','')."
	    )";
	    $result = $db_link->query($sql);        
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  }
	
	  foreach ($product['expensesClassification'] as $xarak) {
	  
	    $aade_katigoria_xarakt_eksodon_id=0;
	    $aade_typos_xarakt_eksodon_id=0;
	    $acc_inv_product_expenses_ammount=$xarak['amount'];
	    if ($xarak['classificationCategory']!='') {
	      $sql="select id_aade_katigoria_xarakt_eksodon from gks_aade_katigoria_xarakt_eksodon 
	      where aade_katigoria_xarakt_eksodon_code like '".$db_link->escape_string($xarak['classificationCategory'])."'";
	      $result = $db_link->query($sql);        
	      if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	      if ($result->num_rows>=1) {
	        $row = $result->fetch_assoc();      
	        $aade_katigoria_xarakt_eksodon_id=$row['id_aade_katigoria_xarakt_eksodon'];
	      }
	    }
	    if ($xarak['classificationType']!='') {
	      $sql="select id_aade_typos_xarakt_eksodon from gks_aade_typos_xarakt_eksodon 
	      where aade_typos_xarakt_eksodon_code like '".$db_link->escape_string($xarak['classificationType'])."'";
	      $result = $db_link->query($sql);        
	      if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;} 
	      if ($result->num_rows>=1) {
	        $row = $result->fetch_assoc();      
	        $aade_typos_xarakt_eksodon_id=$row['id_aade_typos_xarakt_eksodon'];
	      }
	    }
	    
	    $sql="insert into gks_acc_inv_products_expenses (
	    acc_inv_product_id,aade_katigoria_xarakt_eksodon_id,aade_typos_xarakt_eksodon_id,acc_inv_product_expenses_ammount
	    ) values (
	    ".$id_acc_inv_product.",
	    ".$aade_katigoria_xarakt_eksodon_id.",
	    ".$aade_typos_xarakt_eksodon_id.",
	    ".number_format($acc_inv_product_expenses_ammount, 4,'.','')."
	    )";
	    $result = $db_link->query($sql);        
	    if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql); return $ret;}
	  }
	
	}

	$html_out='<a href="admin-acc-inv-item.php?id='.$id.'" class="alert-link">#'.$id.'</a>'.
        ' <span class="acc_inv_state_010draft">'.getAccInvStateDescr('010draft').'</span>';  

	//print '<pre>';print_r($data);print_r($myinvoice);die();

	$ret['message']=$html_out;
	$ret['link']=GKS_SITE_URL.'my/admin-acc-inv-item.php?id='.$id;
	$ret['id']=$id;
	
	$ret['success']=true;
	return $ret;	   
		
	
}


