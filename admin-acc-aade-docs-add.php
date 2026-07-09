<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Προσθήκη εγγράφου ΑΑΔΕ στο σύστημα μέσω myData');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_aade_docs','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']),'html'=>base64_encode($perm_ret['message']));echo json_encode($return); die();}

//echo '<pre>';print_r($gks_user_settings);die();


$mark=''; if (isset($_POST['mark'])) $mark=trim_gks($_POST['mark']);
if ($mark=='') {debug_mail(false,'mark is empty',                  gks_lang('Δεν έχει ορισθεί το ΜΑΡΚ').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το ΜΑΡΚ').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();}

//echo '<pre>';echo $mark; die();
$fullpath=GKS_SITE_PATH.'tmp/'.$mark.'.xml';
if (file_exists($fullpath)==false) {debug_mail(false,'file not found',gks_lang('Δεν βρέθηκε το σχετικό αρχείο').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
    $return = array('success' => false, 'message' => base64_encode(   gks_lang('Δεν βρέθηκε το σχετικό αρχείο').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();}

$parts=explode('_',$mark);
if (count($parts)!=4) {debug_mail(false,'mark is not ok',          gks_lang('Δεν βρέθηκε το σχετικό αρχείο').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αρχείο').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();}

$company_id=intval($parts[0]);
$company_sub_id=intval($parts[1]);
$operation=intval($parts[2]);
$mark=trim_gks($parts[3]);
//echo '<pre>';print $company_id.'|'.$company_sub_id.'|'.$operation.'|'.$mark; die();
  
  
$errors=array();
try {
  
  $xml = new SimpleXMLElement(file_get_contents($fullpath), LIBXML_NOERROR);
  if (isset($xml->invoicesDoc)) {
    
    $NS = array( 
    //  'xsi'  => 'http://www.w3.org/2001/XMLSchema-instance',
      'icls' => 'https://www.aade.gr/myDATA/incomeClassificaton/v1.0',
      'ecls' => 'https://www.aade.gr/myDATA/expensesClassificaton/v1.0',
    ); 
    //$xml->registerXPathNamespace('icls', $NS['xsi']);
    $xml->registerXPathNamespace('icls', $NS['icls']);
    $xml->registerXPathNamespace('ecls', $NS['ecls']);

    //$namespaces = $xml->getNamespaces(true);
    //var_dump($namespaces);die();
    
    
    $invoicesDoc=$xml->invoicesDoc;
    $invoice=$xml->invoicesDoc->invoice;


    $SenderVAT=''; if (isset($invoice->SenderVAT)) $SenderVAT=(string)$invoice->SenderVAT;  
    
    if (isset($invoice->mark)) {
      $mark_doc=(string)$invoice->mark;
    } else {
      $mark_doc='';
    }
    if (isset($invoice->uid)) {
      $invoiceuid=(string)$invoice->uid;
    } else {
      $invoiceuid='';
    }
    if (isset($invoice->qrCodeUrl)) {
      $qrCodeUrl=(string)$invoice->qrCodeUrl;
    } else {
      $qrCodeUrl='';
    }
    if (isset($invoice->downloadingInvoiceUrl)) {
      $downloadingInvoiceUrl=(string)$invoice->downloadingInvoiceUrl;
    } else {
      $downloadingInvoiceUrl='';
    }    
    
    $issuer_address=[];
    if (isset($invoice->issuer->country)) $issuer_address['country']= (string)$invoice->issuer->country;
    if (isset($invoice->issuer->address->street)) $issuer_address['street']= (string)$invoice->issuer->address->street;
    if (isset($invoice->issuer->address->number)) $issuer_address['number']= (string)$invoice->issuer->address->number;
    if (isset($invoice->issuer->address->postalCode)) $issuer_address['postalCode']= (string)$invoice->issuer->address->postalCode;
    if (isset($invoice->issuer->address->city)) $issuer_address['city']= (string)$invoice->issuer->address->city;
      
    //print '<pre>';print_r($issuer_address);die();
    
    if (isset($invoice->issuer->vatNumber)) {
      $afm_issuer=(string)$invoice->issuer->vatNumber;
    } else {
      $afm_issuer='';
    }
    
    
    $counterpart_address=[];
    if (isset($invoice->counterpart->country)) $counterpart_address['country']= (string)$invoice->counterpart->country;
    if (isset($invoice->counterpart->address->street)) $counterpart_address['street']= (string)$invoice->counterpart->address->street;
    if (isset($invoice->counterpart->address->number)) $counterpart_address['number']= (string)$invoice->counterpart->address->number;
    if (isset($invoice->counterpart->address->postalCode)) $counterpart_address['postalCode']= (string)$invoice->counterpart->address->postalCode;
    if (isset($invoice->counterpart->address->city)) $counterpart_address['city']= (string)$invoice->counterpart->address->city;

    //print '<pre>';print_r($counterpart_address);die();
    
    if (isset($invoice->counterpart->vatNumber)) {
      $afm_counterpart=(string)$invoice->counterpart->vatNumber;
    } else {
      $afm_counterpart='';
    }
    
    
     
    if (isset($invoice->invoiceHeader->issueDate)) {
      $issueDate=(string)$invoice->invoiceHeader->issueDate;
    } else {
      $issueDate='';
    } 
    
    if (isset($invoice->invoiceHeader->invoiceType)) {
      $invoiceType=(string)$invoice->invoiceHeader->invoiceType;
    } else {
      $invoiceType='';
    }         
    if (isset($invoice->invoiceHeader->series)) {
      $seira=(string)$invoice->invoiceHeader->series;
    } else {
      $seira='';
    }         
    if (isset($invoice->invoiceHeader->aa)) {
      $my_aa=(string)$invoice->invoiceHeader->aa;
    } else {
      $my_aa='';
    }         
    
    $isDeliveryNote=false;
    if (isset($invoice->invoiceHeader->isDeliveryNote)) {
      $isDeliveryNote=(bool)$invoice->invoiceHeader->isDeliveryNote;
    }
    //echo '<pre>'.$isDeliveryNote;die();
    
    $otherDeliveryNoteHeader=[];
    if ($isDeliveryNote) {
      $otherDeliveryNoteHeader['loadingAddress']=[];
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->street)) {
        $otherDeliveryNoteHeader['loadingAddress']['street']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->street;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->number)) {
        $otherDeliveryNoteHeader['loadingAddress']['number']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->number;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->postalCode)) {
        $otherDeliveryNoteHeader['loadingAddress']['postalCode']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->postalCode;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->city)) {
        $otherDeliveryNoteHeader['loadingAddress']['city']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->loadingAddress->city;
      }
      $otherDeliveryNoteHeader['deliveryAddress']=[];
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->street)) {
        $otherDeliveryNoteHeader['deliveryAddress']['street']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->street;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->number)) {
        $otherDeliveryNoteHeader['deliveryAddress']['number']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->number;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->postalCode)) {
        $otherDeliveryNoteHeader['deliveryAddress']['postalCode']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->postalCode;
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->city)) {
        $otherDeliveryNoteHeader['deliveryAddress']['city']=(string)$invoice->invoiceHeader->otherDeliveryNoteHeader->deliveryAddress->city;
      }
      
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->startShippingBranch)) {
        $otherDeliveryNoteHeader['startShippingBranch']=intval((string)$invoice->invoiceHeader->otherDeliveryNoteHeader->startShippingBranch);
      }
      if (isset($invoice->invoiceHeader->otherDeliveryNoteHeader->completeShippingBranch)) {
        $otherDeliveryNoteHeader['completeShippingBranch']=intval((string)$invoice->invoiceHeader->otherDeliveryNoteHeader->completeShippingBranch);
      }
      
    }
    //echo '<pre>';print_r($otherDeliveryNoteHeader);die();

    $multipleConnectedMarks=[];
    if (isset($invoice->invoiceHeader->multipleConnectedMarks)) {
      foreach ($invoice->invoiceHeader->multipleConnectedMarks as $mcm) {
        $multipleConnectedMarks[]=(string)$mcm;
      }
    }
    //echo '<pre>';print_r($multipleConnectedMarks);die();


    $paymentMethods=[];
    if (isset($invoice->paymentMethods->paymentMethodDetails)) {
      foreach ($invoice->paymentMethods->children() as $pdetail) {
        $pm_type=intval((string)$pdetail->type);
        $pm_amount=floatval((string)$pdetail->amount);
        $pm_paymentMethodInfo=trim_gks((string)$pdetail->paymentMethodInfo);
        $paymentMethods[]=array(
          'type'=>$pm_type,
          'amount'=>$pm_amount,
          'paymentMethodInfo'=>$pm_paymentMethodInfo,
          'payment_acquirer_id'=>0,
        );
      }
    }
    //echo '<pre>';print_r($paymentMethods);die();
    
    $vehicleNumber=''; if (isset($invoice->invoiceHeader->vehicleNumber)) $vehicleNumber=(string)$invoice->invoiceHeader->vehicleNumber;  
    
    $currency=''; if (isset($invoice->invoiceHeader->currency)) $currency=(string)$invoice->invoiceHeader->currency;  
    
    $correlatedInvoices='';if (isset($invoice->invoiceHeader->correlatedInvoices)) $correlatedInvoices=(string)$invoice->invoiceHeader->correlatedInvoices;  
    
    
    
    $totalNetValue=0;         if (isset($invoice->invoiceSummary->totalNetValue))         $totalNetValue=floatval((string)$invoice->invoiceSummary->totalNetValue);  
    $totalVatAmount=0;        if (isset($invoice->invoiceSummary->totalVatAmount))        $totalVatAmount=floatval((string)$invoice->invoiceSummary->totalVatAmount);  
    $totalWithheldAmount=0;   if (isset($invoice->invoiceSummary->totalWithheldAmount))   $totalWithheldAmount=floatval((string)$invoice->invoiceSummary->totalWithheldAmount);  
    $totalFeesAmount=0;       if (isset($invoice->invoiceSummary->totalFeesAmount))       $totalFeesAmount=floatval((string)$invoice->invoiceSummary->totalFeesAmount);  
    $totalStampDutyAmount=0;  if (isset($invoice->invoiceSummary->totalStampDutyAmount))  $totalStampDutyAmount=floatval((string)$invoice->invoiceSummary->totalStampDutyAmount);  
    $totalOtherTaxesAmount=0; if (isset($invoice->invoiceSummary->totalOtherTaxesAmount)) $totalOtherTaxesAmount=floatval((string)$invoice->invoiceSummary->totalOtherTaxesAmount);  
    $totalDeductionsAmount=0; if (isset($invoice->invoiceSummary->totalDeductionsAmount)) $totalDeductionsAmount=floatval((string)$invoice->invoiceSummary->totalDeductionsAmount);  
    $totalGrossValue=0;       if (isset($invoice->invoiceSummary->totalGrossValue))       $totalGrossValue=floatval((string)$invoice->invoiceSummary->totalGrossValue);  
    
    


    
    $aade_skopos_diakinisis_code=0; if (isset($invoice->invoiceHeader->movePurpose)) $aade_skopos_diakinisis_code=intval((string)$invoice->invoiceHeader->movePurpose);
    
    $packingsDeclarations=[];
    if (isset($invoice->packingsDeclarations)) {
      foreach ($invoice->packingsDeclarations->Packages as $Package) {
        $otherPackagingTypeTitle='';
        if (isset($Package->otherPackagingTypeTitle)) $otherPackagingTypeTitle=(string)$Package->otherPackagingTypeTitle;
        $packingsDeclarations[]=array(
          'type'=>intval((string)$Package->packagingType),
          'type_6_descr'=>$otherPackagingTypeTitle,
          'quantity'=>intval((string)$Package->quantity),
        );
      }
    }
    //echo '<pre>';print_r($packingsDeclarations);die();  
    
    //$ $xml->xpath('//to');
    
    
    $otherCorrelatedEntities=[];
    if (isset($invoice->invoiceHeader->otherCorrelatedEntities)) {
      foreach ($invoice->invoiceHeader->otherCorrelatedEntities as $oce) {
        //entityData
          $ed_vatNumber='';
          $ed_country='';
          $ed_branch='';
          $ed_name='';
          //address
            $eda_street='';
            $eda_number='';
            $eda_postalCode='';
            $eda_city='';
        if (isset($oce->entityData)) {
          if (isset($oce->entityData->vatNumber)) $ed_vatNumber=(string) $oce->entityData->vatNumber;
          if (isset($oce->entityData->country)) $ed_country=(string) $oce->entityData->country;
          if (isset($oce->entityData->branch)) $ed_branch=(string) $oce->entityData->branch;
          if (isset($oce->entityData->name)) $ed_name=(string) $oce->entityData->name;
          if (isset($oce->entityData->address)) {
            if (isset($oce->entityData->address->street)) $ed_street=(string) $oce->entityData->address->street;
            if (isset($oce->entityData->address->number)) $ed_number=(string) $oce->entityData->address->number;
            if (isset($oce->entityData->address->postalCode)) $ed_postalCode=(string) $oce->entityData->address->postalCode;
            if (isset($oce->entityData->address->city)) $eda_city=(string) $oce->entityData->address->city;
          }
        }            
        $otherCorrelatedEntities[]=array(
          'type'=>intval((string) $oce->type),
          'vatNumber'=>$ed_vatNumber,
          'country'=>$ed_country,
          'branch'=>$ed_branch,
          'name'=>$ed_name,
          'street'=>$ed_street,
          'number'=>$ed_number,
          'postalCode'=>$ed_postalCode,
          'city'=>$eda_city,
          'user_id'=>0,
          'country_id'=>0,
        );
      }
    }
    
    foreach ($otherCorrelatedEntities as &$oce) {
      if ($oce['vatNumber']!='') {
        $sql="select user_id from gks_users where afm='".$db_link->escape_string($oce['vatNumber'])."' order by user_id desc";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        
        if ($result->num_rows>=1) { 
          $row = $result->fetch_assoc();
          $oce['user_id']=$row['user_id'];
        }         
      }
      if ($oce['country']!='') {
        $sql="select id_country from gks_country where country_initials='".$db_link->escape_string($oce['country'])."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        
        if ($result->num_rows>=1) { 
          $row = $result->fetch_assoc();
          $oce['country_id']=$row['id_country'];
        }         
      }      
    }
    unset($oce);
     
    
    //print '<pre>';print_r($otherCorrelatedEntities);die();
    
    $products=array();
    
    if (isset($invoice->invoiceDetails)) {
      
      foreach ($invoice->invoiceDetails as $product) {
        $lineNumber=0; if (isset($product->lineNumber)) $lineNumber=intval((string) $product->lineNumber);
        
        $itemCode='';  if (isset($product->itemCode))  $itemCode =trim_gks((string)$product->itemCode);
        $itemDescr=''; if (isset($product->itemDescr)) $itemDescr=trim_gks((string)$product->itemDescr);
        $TaricNo='';   if (isset($product->TaricNo))   $TaricNo=trim_gks((string)$product->TaricNo);
        
        $quantity=0; if (isset($product->quantity)) $quantity=floatval((string) $product->quantity);
        $measurementUnit=0; if (isset($product->measurementUnit)) $measurementUnit=intval((string) $product->measurementUnit);
        $netValue=0; if (isset($product->netValue)) $netValue=floatval((string) $product->netValue);
        $vatCategory=0; if (isset($product->vatCategory)) $vatCategory=intval((string) $product->vatCategory);
        $vatAmount=0; if (isset($product->vatAmount)) $vatAmount=floatval((string) $product->vatAmount);
        $vatExemptionCategory=0; if (isset($product->vatExemptionCategory)) $vatExemptionCategory=intval((string) $product->vatExemptionCategory);
        
        $withheldAmount=0; if (isset($product->withheldAmount)) $withheldAmount=floatval((string) $product->withheldAmount);
        $withheldPercentCategory=0; if (isset($product->withheldPercentCategory)) $withheldPercentCategory=intval((string) $product->withheldPercentCategory);
        
        $stampDutyAmount=0; if (isset($product->stampDutyAmount)) $stampDutyAmount=floatval((string) $product->stampDutyAmount);
        $stampDutyPercentCategory=0; if (isset($product->stampDutyPercentCategory)) $stampDutyPercentCategory=intval((string) $product->stampDutyPercentCategory);
        
        $feesAmount=0; if (isset($product->feesAmount)) $feesAmount=floatval((string) $product->feesAmount);
        $feesPercentCategory=0; if (isset($product->feesPercentCategory)) $feesPercentCategory=intval((string) $product->feesPercentCategory);
        
        $otherTaxesAmount=0; if (isset($product->otherTaxesAmount)) $otherTaxesAmount=floatval((string) $product->otherTaxesAmount);
        $otherTaxesPercentCategory=0; if (isset($product->otherTaxesPercentCategory)) $otherTaxesPercentCategory=intval((string) $product->otherTaxesPercentCategory);
        
        $deductionsAmount=0; if (isset($product->deductionsAmount)) $deductionsAmount=floatval((string) $product->deductionsAmount);
        
        $lineComments='';if (isset($product->lineComments)) $lineComments=(string) $product->lineComments;
        
        $incomeClassification=array();
        if (isset($product->incomeClassification)) {
          foreach ($product->incomeClassification as $xarak) {
            $xarak_item = (array)$xarak->children($NS['icls']);
            if (isset($xarak_item['classificationType'])==false) $xarak_item['classificationType']='';
            if (isset($xarak_item['classificationCategory'])==false) $xarak_item['classificationCategory']='';
            if (isset($xarak_item['amount'])==false) $xarak_item['amount']=0;
            $incomeClassification[]=$xarak_item;
          }
        }
        //400001827643201
        
        $expensesClassification=array();
        if (isset($product->expensesClassification)) {
          foreach ($product->expensesClassification as $xarak) {
            $xarak_item = (array)$xarak->children($NS['ecls']);
            if (isset($xarak_item['classificationType'])==false) $xarak_item['classificationType']='';
            if (isset($xarak_item['classificationCategory'])==false) $xarak_item['classificationCategory']='';
            if (isset($xarak_item['amount'])==false) $xarak_item['amount']=0;
            $expensesClassification[]=$xarak_item;
          }
        }

        
        
        
        
        
        $products[]=array(
          'lineNumber' => $lineNumber,
          'itemCode'=> $itemCode,
          'itemDescr'=> $itemDescr,
          'TaricNo'=> $TaricNo,

          
          'quantity' => $quantity,
          'measurementUnit' => $measurementUnit,
          'netValue' => $netValue,
          'vatCategory' => $vatCategory,
          'vatAmount' => $vatAmount,
          'vatExemptionCategory' => $vatExemptionCategory,
          
          'withheldAmount' => $withheldAmount,
          'withheldPercentCategory' => $withheldPercentCategory,
          'stampDutyAmount' => $stampDutyAmount,
          'stampDutyPercentCategory' => $stampDutyPercentCategory,
          'feesAmount' => $feesAmount,
          'feesPercentCategory' => $feesPercentCategory,
          'otherTaxesAmount' => $otherTaxesAmount,
          'otherTaxesPercentCategory' => $otherTaxesPercentCategory,
          'deductionsAmount' => $deductionsAmount,
          'lineComments' => $lineComments,
          
          'incomeClassification' => $incomeClassification,
          'expensesClassification' => $expensesClassification,

        );
        
      }
        
      
    }
    
    
    
    //echo '<pre>';print_r($products);die();
    
    
    $myinvoice=array(
      'filename' => $fullpath,
      'invoiceuid' => $invoiceuid,
      'mark' => $mark_doc,
      'qrurl' => $qrCodeUrl,
      'paroxos_qrurl' => $downloadingInvoiceUrl,
      'afm_issuer' => $afm_issuer,
      'issuer_address'=>$issuer_address,
      'user_id_issuer' => 0,
      'afm_counterpart' => $afm_counterpart,
      'counterpart_address'=>$counterpart_address,
      'user_id_counterpart' => 0,
      'SenderVAT' => $SenderVAT,
      'issueDate'=> $issueDate,
      'issueDateint'=> strtotime($issueDate),
      
      'text'=> '',
      'invoiceType' => $invoiceType,
      'inv_type_descr' => '',
      'seira' => $seira,
      'number' => intval($my_aa),
      'isDeliveryNote'=>$isDeliveryNote,
      'otherDeliveryNoteHeader'=>$otherDeliveryNoteHeader,
      
      'currency' => $currency,
      'correlatedInvoices'=>$correlatedInvoices,
      'multipleConnectedMarks' => $multipleConnectedMarks,
      
      'vehicleNumber' => $vehicleNumber,
      'gks_price_net' => $totalNetValue,
      'gks_price_fpa' => $totalVatAmount,
      'gks_price_netfpa' => $totalNetValue + $totalVatAmount,
      'totalWithheldAmount' => $totalWithheldAmount,
      'totalFeesAmount' => $totalFeesAmount,
      'totalStampDutyamount' => $totalStampDutyAmount,
      'totalOtherTaxesAmount' => $totalOtherTaxesAmount,
      'totalDeductionsAmount' => $totalDeductionsAmount,
      'gks_price_total' => $totalGrossValue,
      
      'paymentMethods' => $paymentMethods,

      'aade_skopos_diakinisis_code' => $aade_skopos_diakinisis_code,
      'packingsDeclarations'=>$packingsDeclarations,
      'otherCorrelatedEntities'=>$otherCorrelatedEntities,
      
      'products' => $products,
    );
    $myinvoice['id_acc_inv']=0;
    $myinvoice['id_whi_mov']=0;
    
     
  } else {
    
    $errors[]=gks_lang('Δεν βρέθηκε το invoicesDoc στην αρχή του αρχείου XML');
  }  
  
} catch (Exception $e) { 
  $errors[]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
}

if (count($errors)>0) {  
  $return = array('success' => false, 'message' => base64_encode(implode('<br>',$errors)));
  echo json_encode($return); die();}

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myinvoice,true)));
//echo json_encode($return); die();

//print '<pre>';print_r($myinvoice);die();
if ($myinvoice['currency']=='' and $myinvoice['invoiceType']!='9.3') {debug_mail(false,'currency',$myinvoice['currency']);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myinvoice['currency'],gks_lang('Το παραστατικό έχει ως το νόμισμα το <b>[1]</b> το οποίο δεν υποστηρίζεται ακόμα'))));
    echo json_encode($return); die();}
  
if ($myinvoice['mark']=='') {debug_mail(false,'mark');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το ΜΑΡΚ στο αρχείο XML της ΑΑΔΕ')));
    echo json_encode($return); die();}

if ($myinvoice['afm_issuer']=='') {debug_mail(false,'afm_issuer');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο εκδότης στο αρχείο XML της ΑΑΔΕ')));
    echo json_encode($return); die();}
  
if ($myinvoice['afm_counterpart']=='') {debug_mail(false,'afm_counterpart',);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο Λήπτης στο αρχείο XML της ΑΑΔΕ')));
    echo json_encode($return); die();}

if ($myinvoice['invoiceType']=='') {debug_mail(false,'afm_counterpart');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο τύπος παραστατικού στο αρχείο XML της ΑΑΔΕ')));
    echo json_encode($return); die();}
  
$mytable='gks_acc_inv';
$myidtable='id_acc_inv';
$myidrtable='acc_inv_id';
$mystatetable='inv_state';
$myftable='inv_acc';
$mygtable='inv_guid';
$mydtable='inv_date';

if ($myinvoice['invoiceType']=='9.3') {
  $mytable='gks_whi_mov';
  $myidtable='id_whi_mov';
  $myidrtable='whi_mov_id';
  $mystatetable='mov_state';
  $myftable='mov_whi';
  $mygtable='mov_guid';
  $mydtable='mov_date';

}   
  
$sql="SELECT ".$myidtable.", aade_invoicemark,".$mystatetable." 
FROM ".$mytable." 
WHERE aade_invoicemark like '".$db_link->escape_string($myinvoice['mark'])."'
and ".$mytable.".company_id=".$company_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  if ($myinvoice['invoiceType']=='9.3') {
    $tmpmsg=gks_lang('Υπάρχει ήδη στο σύστημα δελτίο με ΜΑΡΚ <b>[1]</b><br>για αυτήν την εταιρεία<br>και είναι το: <a href="admin-whi-mov-item.php?id=[2]" class="gks_link">#[2]</a> <span class="whi_mov_state_[3]">[4]</span>');
  } else {
    $tmpmsg=gks_lang('Υπάρχει ήδη στο σύστημα παραστατικό με ΜΑΡΚ <b>[1]</b><br>για αυτήν την εταιρεία<br>και είναι το: <a href="admin-acc-inv-item.php?id=[2]" class="gks_link">#[2]</a> <span class="acc_inv_state_[3]">[4]</span>');
  }
  $tmpmsg=str_replace('[1]',$myinvoice['mark'],$tmpmsg);
  $tmpmsg=str_replace('[2]',$row[$myidtable],$tmpmsg);
  $tmpmsg=str_replace('[3]',$row[$mystatetable],$tmpmsg);
  $tmpmsg=str_replace('[4]',getAccInvStateDescr($row[$mystatetable]),$tmpmsg);
  $tmpmsg.='<br>'.gks_lang('Ανανεώστε την σελίδα');

  debug_mail(false,'mark exist',$tmpmsg);
  $return = array('success' => false, 'message' => base64_encode($tmpmsg));
  echo json_encode($return); die();}


$sql="select * from gks_acc_eidi_parastatikon where eidos_parastatikou_aade_code='".$db_link->escape_string($myinvoice['invoiceType'])."'";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows==0) {
  $row = $result->fetch_assoc();
  debug_mail(false,'invoiceType not exist, need update');
  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myinvoice['invoiceType'],gks_lang('Δεν βρέθηκε ο τύπος παραστατικού <b>[1]</b> στο σύστημα')).'<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος')));
  echo json_encode($return); die();}
$row_parast = $result->fetch_assoc();
//echo '<pre>';print_r($row_parast);die();
//echo '<pre>';print_r($myinvoice);die();
  
$myinvoice['from_aade_import']='';
if ($operation==0) {
  $sql="SELECT id_company, company_title, company_eponimia, company_afm FROM gks_company WHERE company_afm like '".$db_link->escape_string($myinvoice['afm_issuer'])."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $myinvoice['from_aade_import']='diko_mou';
    $myinvoice['company_id']=$row['id_company'];
    $myinvoice['company_title']=$row['company_title'];
    $myinvoice['company_eponimia']=$row['company_eponimia'];
    $myinvoice['company_afm']=$row['company_afm'];

  }
}

//echo '<pre>';print_r($myinvoice);die();
if ($operation==1) {
  $sql="SELECT id_company, company_title, company_eponimia, company_afm FROM gks_company WHERE company_afm like '".$db_link->escape_string($myinvoice['afm_counterpart'])."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $myinvoice['from_aade_import']='apo_allon';
    $myinvoice['company_id']=$row['id_company'];
    $myinvoice['company_title']=$row['company_title'];
    $myinvoice['company_eponimia']=$row['company_eponimia'];
    $myinvoice['company_afm']=$row['company_afm'];

  }
}

if ($myinvoice['from_aade_import']=='') {debug_mail(false,'afm_counterpart');
  $tmpmsg=gks_lang('Ούτε το ΑΦΜ <b>[1]</b> αλλά και ούτε το ΑΦΜ <b>[2]</b> ανήκουν σε κάποια εταιρεία σας');
  $tmpmsg=str_replace('[1]',$myinvoice['afm_issuer'],$tmpmsg);
  $tmpmsg=str_replace('[2]',$myinvoice['afm_counterpart'],$tmpmsg);  
  $tmpmsg.='<br>'.gks_lang('Μήπως δεν σας αφορά το συγκεκριμένο παραστατικό ;');
  $return = array('success' => false, 'message' => base64_encode($tmpmsg));
  echo json_encode($return); die();}

//echo '<pre>';print_r($myinvoice);die();
        
$myinvoice['user_id']=0;
$myinvoice['user_afm']=$myinvoice['from_aade_import']=='diko_mou' ? $myinvoice['afm_counterpart'] : $myinvoice['afm_issuer'];
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
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 

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
  
if ($myinvoice['user_id']==0) {debug_mail(false,'afm_counterpart');
  $tmpmsg=gks_lang('Στις επαφές σας δεν βρέθηκε ο αντισυμβαλλόμενος με ΑΦΜ <b>[1]</b>');
  $tmpmsg=str_replace('[1]',$myinvoice['user_afm'],$tmpmsg);  
  $tmpmsg.='<br>'.gks_lang('Δημιουργήστε πρώτα την επαφή χρησιμοποιώντας το σχετικό εικονίδιο και δοκιμάστε ξανά');
  $return = array('success' => false, 'message' => base64_encode($tmpmsg));
  echo json_encode($return); die();}


if (isset($myinvoice['issuer_address'])) {
  $id_country=0;
  if (isset($myinvoice['issuer_address']['country']) and trim($myinvoice['issuer_address']['country'])!='') {
    $sql="select id_country from gks_country where country_initials='".$db_link->escape_string($myinvoice['issuer_address']['country'])."'";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    
    if ($result->num_rows>=1) { 
      $row = $result->fetch_assoc();
      $id_country=$row['id_country'];
    }
  }
  if ($id_country>0 and 
      isset($myinvoice['issuer_address']['street']) and trim($myinvoice['issuer_address']['street'])!='' and
      isset($myinvoice['issuer_address']['number']) and trim($myinvoice['issuer_address']['number'])!='' and
      isset($myinvoice['issuer_address']['postalCode']) and trim($myinvoice['issuer_address']['postalCode'])!='' and
      isset($myinvoice['issuer_address']['city']) and trim($myinvoice['issuer_address']['city'])!='') {

    if ($myinvoice['ma_country_id']==$id_country and 
        $myinvoice['ma_tk']==$myinvoice['issuer_address']['postalCode']) {
      //einai omoio
    } else { //einai allo
      $myinvoice['ma_nomos_id']=0; //trim_gks($row['ma_nomos_id']);
    }
    if ($myinvoice['ma_country_id']==$id_country and 
        $myinvoice['ma_tk']==$myinvoice['issuer_address']['postalCode'] and
        $myinvoice['ma_poli']==$myinvoice['issuer_address']['city'] and
        $myinvoice['ma_odos']==$myinvoice['issuer_address']['street']) {
      //einai omoio
    } else { //einai allo
      $myinvoice['ma_orofos']=''; //trim_gks($row['ma_orofos']);
      $myinvoice['ma_perioxi']=''; //trim_gks($row['ma_perioxi']);
    }
        
    $myinvoice['ma_odos']=$myinvoice['issuer_address']['street'];
    $myinvoice['ma_arithmos']=$myinvoice['issuer_address']['number'];
    $myinvoice['ma_poli']=$myinvoice['issuer_address']['city'];
    $myinvoice['ma_tk']=$myinvoice['issuer_address']['postalCode'];
    $myinvoice['ma_country_id']=$id_country;
  }        
  //echo '<pre>'.$id_country;die(); 
  
}

$myinvoice['aade_user_id']=0; //$my_wp_user_id
if ($myinvoice['SenderVAT']!='') {
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm like '".$db_link->escape_string($myinvoice['SenderVAT'])."'
  order by ".GKS_WP_TABLE_PREFIX."users.ID";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $myinvoice['aade_user_id']=$row['ID'];
  }
}

$myinvoice['tropos_pliromis']=1;

foreach ($myinvoice['paymentMethods'] as &$pm) {
  
  $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
  WHERE aade_tropos_pliromis_id=".$pm['type']." 
  AND payment_acquirer_name Like '".$db_link->escape_string($pm['paymentMethodInfo'])."'
  and payment_acquirer_disabled=0
  order by id_payment_acquirer";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $pm['payment_acquirer_id']=$row['id_payment_acquirer'];
  }
  
  if ($pm['payment_acquirer_id']==1) {
    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
    WHERE aade_tropos_pliromis_id=".$pm['type']." 
    AND payment_acquirer_name Like '%".$db_link->escape_string($pm['paymentMethodInfo'])."%'
    and payment_acquirer_disabled=0
    order by id_payment_acquirer";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $pm['payment_acquirer_id']=$row['id_payment_acquirer'];
    }    
  }
  if ($pm['payment_acquirer_id']==1) {
    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
    WHERE aade_tropos_pliromis_id=".$pm['type']." 
    and payment_acquirer_disabled=0
    order by id_payment_acquirer";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $pm['payment_acquirer_id']=$row['id_payment_acquirer'];
    }    
  }
  if ($pm['payment_acquirer_id']==1) {
    $sql="SELECT id_payment_acquirer FROM gks_payment_acquirers 
    WHERE aade_tropos_pliromis_id=".$pm['type']." 
    AND payment_acquirer_name Like '%".$db_link->escape_string($pm['paymentMethodInfo'])."%'
    order by id_payment_acquirer";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $pm['payment_acquirer_id']=$row['id_payment_acquirer'];
    }    
  }
}
unset($pm);

if (isset($myinvoice['paymentMethods']) and count($myinvoice['paymentMethods'])>=1) { 
  $max_ammount=0;
  foreach ($myinvoice['paymentMethods'] as $pm) {
    if ($pm['amount']>$max_ammount) {
      $myinvoice['tropos_pliromis']=$pm['payment_acquirer_id'];
      $max_ammount=$pm['amount'];
    }
  }
}
//echo '<pre>'.$myinvoice['tropos_pliromis']; print_r($myinvoice['paymentMethods']);die();

if ($myinvoice['tropos_pliromis']<=1 and 
    isset($gks_user_settings[$mytable]['tropos_pliromis']) and 
    $gks_user_settings[$mytable]['tropos_pliromis']>1) {
  $myinvoice['tropos_pliromis']=$gks_user_settings[$mytable]['tropos_pliromis'];
  
}

//echo '<pre>'.$myinvoice['tropos_pliromis']; die();

$myinvoice['aade_skopos_diakinisis_id']=0;
if ($myinvoice['aade_skopos_diakinisis_code']>0) {
  $sql="select id_aade_skopos_diakinisis 
  from gks_aade_skopos_diakinisis 
  where aade_skopos_diakinisis_code=".$myinvoice['aade_skopos_diakinisis_code']."
  and aade_disable=0
  order by sortorder";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
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

if ($isDeliveryNote) {
  $sql.=" AND gks_acc_journal.acc_eidos_parastatikou_whi_id>0";
} else {
  $sql.=" AND gks_acc_journal.acc_eidos_parastatikou_whi_id=0";
}

if ($myinvoice['from_aade_import']=='diko_mou') {
  $sql.=" AND gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code='".$db_link->escape_string($invoiceType)."'";
} else {
  $sql.=" AND gks_acc_eidi_parastatikon.import_apo_allon like '%[".$db_link->escape_string($invoiceType)."]%'";
}
//gks_acc_journal.acc_eidos_parastatikou_id=502
//echo '<pre>'.$sql;die();


$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows==0) {
  
  $sql2="select company_title from gks_company where id_company=".$company_id;
  $result2 = $db_link->query($sql2);
  if (!$result2) {
    debug_mail(false,'error sql',$sql2);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result2->num_rows==0) {
    debug_mail(false,'company not found',$sql2);
    $tmpmsg=gks_lang('Δεν βρέθηκε η εταιρεία με ID <br><b>[1]</b><br>και όνομα<br><b>[2]</b>');
    $tmpmsg=str_replace('[1]',$company_id,$tmpmsg);
    $tmpmsg=str_replace('[2]',$myinvoice['company_title'],$tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die(); }
  $row2 = $result2->fetch_assoc();
  $company_title_out=$row2['company_title'];
  
  if ($company_sub_id==0) {
    $company_title_out.=' \ '.gks_lang('Κεντρικό');
  } else {
    $sql2="select company_sub_title from gks_company_subs where id_company_sub=".$company_sub_id;
    $result2 = $db_link->query($sql2);
    if (!$result2) {
      debug_mail(false,'error sql',$sql2);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result2->num_rows==0) {
      debug_mail(false,'subcompany not found',$sql2);
      $tmpmsg=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID <br><b>[1]</b><br>της εταιρείας<br><b>[2]</b>');
      $tmpmsg=str_replace('[1]',$company_sub_id,$tmpmsg);
      $tmpmsg=str_replace('[2]',$company_title_out,$tmpmsg);      
      $return = array('success' => false, 'message' => base64_encode());
      echo json_encode($return); die(); }
    $row2 = $result2->fetch_assoc();
    $company_title_out.=' \ '.$row2['company_sub_title'];
  }
  
  $sql2="select eidos_parastatikou_descr from gks_acc_eidi_parastatikon where eidos_parastatikou_aade_code='".$db_link->escape_string($invoiceType)."'";
  $result2 = $db_link->query($sql2);
  if (!$result2) {
    debug_mail(false,'error sql',$sql2);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result2->num_rows==0) {
    debug_mail(false,'eidos_parastatikou not found',$sql2);
    $tmpmsg=gks_lang('Δεν βρέθηκε ο τύπος παραστατικών με κωδικό<br><b>[1]</b><br>στο σύστημα');
    $tmpmsg=str_replace('[1]',$invoiceType,$tmpmsg);
    $tmpmsg='<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος');
    $return = array('success' => false, 'message' => base64_encode());
    echo json_encode($return); die(); }
  $row2 = $result2->fetch_assoc();
  $eidos_parastatikou_descr_out=$row2['eidos_parastatikou_descr'].' ('.gks_lang('κωδικός').': '.$invoiceType.')';
  
  if ($myinvoice['from_aade_import']=='diko_mou') {
    $tmpmsg=gks_lang('Δεν βρέθηκε ενεργό ημερολόγιο με τύπο παραστατικού<br><b>[1]</b><br>και αντίστοιχη ενεργή σειρά για την εταιρεία<br><b>[2]</b>');
    if ($isDeliveryNote) {
      $tmpmsg.='<br>'.gks_lang('και να είναι και ενεργοποιημένο το Δελτίο Αποστολής/Παραλαβής');
    }
    $tmpmsg=str_replace('[1]',$eidos_parastatikou_descr_out,$tmpmsg);
    $tmpmsg=str_replace('[2]',$company_title_out,$tmpmsg);
    debug_mail(false,$tmpmsg,$sql);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die(); 
  } else {
    $mesage=gks_lang('Δεν βρέθηκε ενεργό ημερολόγιο που να μπορεί να δεχθεί παραστατικά τύπου<br><b>[1]</b><br>και αντίστοιχη ενεργή σειρά για την εταιρεία<br><b>[2]</b>');
    $mesage=str_replace('[1]',$eidos_parastatikou_descr_out,$mesage);
    $mesage=str_replace('[2]',$company_title_out,$mesage);
    if ($isDeliveryNote) {
      $mesage.='<br>'.gks_lang('και να είναι και ενεργοποιημένο το').' <b>'.gks_lang('Δελτίο Αποστολής/Παραλαβής').'</b>';
    }
    $sql2="select eidos_parastatikou_descr,eidos_parastatikou_aade_code from gks_acc_eidi_parastatikon where import_apo_allon like '%[".$db_link->escape_string($invoiceType)."]%'";
    $result2 = $db_link->query($sql2);
    if (!$result2) {
      debug_mail(false,'error sql',$sql2);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $eidos_parastatikou_descr_array=array();
    while ($row2 = $result2->fetch_assoc()) {
      $eidos_parastatikou_descr_array[]='<b>'.$row2['eidos_parastatikou_descr'].'</b>';
    }
    
    $mesage.='<br><br>';
    if (count($eidos_parastatikou_descr_array)==0) {
      $mesage.=gks_lang('Δεν βρέθηκαν σχετικοί τύποι παραστατικών').'<br>'.gks_lang('Πιθανόν να λυθεί το θέμα στην επόμενη αναβάθμιση του συστήματος');
    } else {
      $mesage.=gks_lang('Τύποι παραστατικών που μπορούν να δεχθούν τον παραπάνω τύπο είναι').':<br>'.implode('<br>',$eidos_parastatikou_descr_array);
    }
    debug_mail(false,'subcompany invoicetype',$mesage."\r\n".$sql);
    $return = array('success' => false, 'message' => base64_encode($mesage));
    echo json_encode($return); die(); 
  }
}
//echo '<pre>'.$sql;die();

$row = $result->fetch_assoc();
$myinvoice[$myftable.'_journal_id']=$row['id_acc_journal'];
$myinvoice[$myftable.'_seira_id']=$row['id_acc_seira'];
$myinvoice[$myftable.'_seira_code']=$row['seira_code'];
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
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
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
//print '<pre>';print $warehouses_id_from.'|'.$warehouses_id_to."\n";print_r($myinvoice);die();

$tropos_apostolis=1;
$products_need_apostoli=0;
if ($myinvoice['warehouses_id_from']>0 or $myinvoice['warehouses_id_to']>0) {
  $tropos_apostolis=2;
  $products_need_apostoli=1;
  if (isset($gks_user_settings[$mytable]['tropos_apostolis']) and 
      $gks_user_settings[$mytable]['tropos_apostolis']>1) {
    $tropos_apostolis=$gks_user_settings[$mytable]['tropos_apostolis'];
    $products_need_apostoli=1;
  }
}



if ($myinvoice['invoiceType']=='9.3') {
  $xxx_guid=guid_for_whi_mov();
} else {
  $xxx_guid=guid_for_acc_inv();
}

$sql="insert into ".$mytable." (
user_id_add,user_id_edit,mydate_add,mydate_edit,myip,".$mygtable."
) values (
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($xxx_guid)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
 
$id = $db_link->insert_id;
$myinvoice['id']=$id;

$sxolio=gks_lang('Προσθήκη από backend, εισαγωγή από ΑΑΔΕ').' '; 
$sql="insert into ".$mytable."_log (".$myidrtable.", add_date,user_id,sxolio) values (
".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
  
$set_filename='';
$save_dir = GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/';
if (file_exists($save_dir) == false) {
  if (@mkdir($save_dir , 0777, true) == false ) {
    $errors[]=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); 
    debug_mail(false,$errors[count($errors)-1]); 
  } else {
    $set_filename='invoice_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999).'-response.xml';
    $full_path=$save_dir.$set_filename;
    file_put_contents($full_path, file_get_contents($fullpath));  
  }
}

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

$sql="update ".$mytable." set
from_aade_import='".$myinvoice['from_aade_import']."',
from_aade_import_json='".$db_link->escape_string($from_aade_import_json)."',

import_".$myftable."_seira_code='".$db_link->escape_string($myinvoice['seira'])."',
import_".$myftable."_number_str='".$db_link->escape_string($myinvoice['number'])."',
import_eidos_parastatikou_aade_code='".$db_link->escape_string($myinvoice['invoiceType'])."',
".$mystatetable."='010draft',
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
".$myftable."_journal_id=".$myinvoice[$myftable.'_journal_id'].",
".$myftable."_seira_id=".$myinvoice[$myftable.'_seira_id'].",
".$myftable."_seira_code='".$db_link->escape_string($myinvoice[$myftable.'_seira_code'])."',
".$mydtable."='".showDate($myinvoice['issueDateint'],'Y-m-d H:i:s',-1)."',
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
vehicle_number='".$db_link->escape_string($myinvoice['vehicleNumber'])."',";

if ($mytable=='gks_acc_inv') {
  $sql.="tropos_pliromis_one_multi=".(count($myinvoice['paymentMethods'])==1 ? '0' : '1').',';
}


if ($myinvoice['invoiceType']!='9.3') {
$sql.="tropos_pliromis=".$myinvoice['tropos_pliromis'].",
products_need_pliromi=".($myinvoice['tropos_pliromis']>=2 ? '1' : '0').",
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

affect_balance=0,
affect_balance_all_poso=1,
affect_balance_all_poso_type='pliroteo',
affect_balance_poso=0,
affect_balance_pros=0,

";
}


if ($myinvoice['isDeliveryNote']) {

  if (isset($myinvoice['otherDeliveryNoteHeader']['loadingAddress'])) {
    if (isset($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['street'])) {
      $sql.="load_odos='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['street'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['number'])) {
      $sql.="load_arithmos='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['number'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['postalCode'])) {
      $sql.="load_tk='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['postalCode'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['city'])) {
      $sql.="load_poli='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['loadingAddress']['city'])."',";
    }
  }
  if (isset($myinvoice['otherDeliveryNoteHeader']['deliveryAddress'])) {
    if (isset($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['street'])) {
      $sql.="deli_odos='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['street'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['number'])) {
      $sql.="deli_arithmos='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['number'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['postalCode'])) {
      $sql.="deli_tk='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['postalCode'])."',";
    }
    if (isset($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['city'])) {
      $sql.="deli_poli='".$db_link->escape_string($myinvoice['otherDeliveryNoteHeader']['deliveryAddress']['city'])."',";
    }
  }
  
  if (isset($myinvoice['otherDeliveryNoteHeader']['startShippingBranch'])) {
    $sql.="load_branch=".$myinvoice['otherDeliveryNoteHeader']['startShippingBranch'].",";
  }
  if (isset($myinvoice['otherDeliveryNoteHeader']['completeShippingBranch'])) {
    $sql.="deli_branch=".$myinvoice['otherDeliveryNoteHeader']['completeShippingBranch'].",";
  }
}

$sql.="
tropos_apostolis=".$tropos_apostolis.",
products_need_apostoli=".$products_need_apostoli.",
warehouses_id_from=".$myinvoice['warehouses_id_from'].",
warehouses_id_to=".$myinvoice['warehouses_id_to'].",
aade_invoiceuid='".$db_link->escape_string($myinvoice['invoiceuid'])."',
aade_invoicemark='".$db_link->escape_string($myinvoice['mark'])."',
aade_qrurl='".$db_link->escape_string($myinvoice['qrurl'])."',
aade_paroxos_qrurl='".$db_link->escape_string($myinvoice['paroxos_qrurl'])."',
aade_statuscode='Success',
aade_send_date='".showDate($myinvoice['issueDateint'],'Y-m-d H:i:s',-1)."',
aade_user_id=".$myinvoice['aade_user_id'].",
aade_xml_response='".$db_link->escape_string($set_filename)."'
where ".$myidtable."=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 

//inv_acc_journal_id=".$inv_acc_journal_id.",
//inv_acc_seira_id=".$inv_acc_seira_id.",

if (isset($myinvoice['otherCorrelatedEntities']) and 
    count($myinvoice['otherCorrelatedEntities'])>0) {
  $entity_aa=0;
  foreach ($myinvoice['otherCorrelatedEntities'] as $oce) {
    $entity_aa++;
    $sql="insert into ".$mytable."_other_entity (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    ".$myidrtable.",aade_entitytype_id,entity_user_id,
    entity_afm,entity_name,entity_branch,entity_odos,
    entity_arithmos,entity_poli,entity_tk,entity_country_id,
    entity_aa
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id.",
    ".$oce['type'].",
    ".$oce['user_id'].",
    '".$db_link->escape_string($oce['vatNumber'])."',
    '".$db_link->escape_string($oce['name'])."',
    ".($oce['branch']=='' ? 'null' : intval($oce['branch'])).",
    '".$db_link->escape_string($oce['street'])."',
    '".$db_link->escape_string($oce['number'])."',
    '".$db_link->escape_string($oce['city'])."',
    '".$db_link->escape_string($oce['postalCode'])."',
    ".$oce['country_id'].",
    ".$entity_aa."
    
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    
  }
}


if (isset($myinvoice['packingsDeclarations']) and 
    count($myinvoice['packingsDeclarations'])>0) {
  $packaging_aa=0;
  foreach ($myinvoice['packingsDeclarations'] as $package) {
    $packaging_aa++;
    $sql="insert into ".$mytable."_packings_declarations (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    ".$myidrtable.",packaging_type_id,packaging_type_6_descr,
    packaging_quantity,packaging_aa
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id.",
    ".$package['type'].",
    '".$db_link->escape_string($package['type_6_descr'])."',
    ".$package['quantity'].",
    ".$packaging_aa."
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    
    
  } 
  
}

if (isset($myinvoice['correlatedInvoices']) and 
    $myinvoice['correlatedInvoices']!='') {
    
  $coi_acc_inv_id=0;
  $coi_acc_pay_id=0;
  $coi_whi_mov_id=0;
  $coi_aa=1;
  
  $sql="insert into ".$mytable."_correlated_invoices (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  ".$myidrtable.",coi_mark,coi_acc_inv_id,coi_acc_pay_id,coi_whi_mov_id,coi_aa
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id.",
  '".$db_link->escape_string($myinvoice['correlatedInvoices'])."',
  ".$coi_acc_inv_id.",
  ".$coi_acc_pay_id.",
  ".$coi_whi_mov_id.",
  ".$coi_aa.")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
}

$mcm_aa=0;
foreach ($multipleConnectedMarks as $mcm_mark) {
  $mcm_acc_inv_id=0;
  $mcm_acc_pay_id=0;
  $mcm_whi_mov_id=0;
  $mcm_aa++;
  
  $sql="insert into ".$mytable."_multiple_connected_marks (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  ".$myidrtable.",mcm_mark,mcm_acc_inv_id,mcm_acc_pay_id,mcm_whi_mov_id,mcm_aa
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id.",
  '".$db_link->escape_string($mcm_mark)."',
  ".$mcm_acc_inv_id.",
  ".$mcm_acc_pay_id.",
  ".$mcm_whi_mov_id.",
  ".$mcm_aa.")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

} 

$pm_pp=0;
foreach ($myinvoice['paymentMethods'] as $pm) {
  $pm_pp++;
  $sql="insert into ".$mytable."_payment (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  ".$myidrtable.",pp,payment_acquirer_id,poso
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id.",
  ".$pm_pp.",
  ".$pm['payment_acquirer_id'].",
  ".$pm['amount'].")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }   
}


$sql_tempate1="SELECT gks_barcodes.product_id,
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
END as product_descr_p
FROM (gks_barcodes 
LEFT JOIN gks_eshop_products ON gks_barcodes.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
";

$sql_tempate2="select * from (
SELECT gks_eshop_products.id_product,
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
END as product_descr_p
FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
) as mytable
";

foreach ($myinvoice['products'] as $product) {

  if ($product['quantity']==0) $product['quantity']=1;
  
  $product_monada_id=0;
  if ($product['measurementUnit']==1) $product_monada_id=1; //temaxia
  else if ($product['measurementUnit']==2) $product_monada_id=11;  //kila
  else if ($product['measurementUnit']==3) $product_monada_id=44;  //litra
  
  $product_fpa_base_id=0;
  $product_fpa_id=0;
  $product_fpa_pososto=0;
  
  $fiscal_position_id=1; //lianikis esoterikou
  if ($row_parast['eidos_parastatikou_need_afm']==1) $fiscal_position_id=11; //xondrikis esoterikou
  
  
  if ($product['vatCategory']>0) {
    $sql="select * from gks_aade_katigoria_fpa where aade_katigoria_fpa_code=".$product['vatCategory'];
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $product_fpa_base_id=$row['fpa_base_id'];
      $product_fpa_pososto=$row['aade_katigoria_fpa_pososto'];
      $product_fpa_id=$row['direct_fpa_id'];
      

    }
  }
  
  //print '<pre>';print_r($product);die();
  
  $product_id=0;
  $product_descr=$product['itemDescr'];
  $product_comments='';
  $aade_lineComments=$product['lineComments'];

  
  
  $itemCode=$product['itemCode'];
  if ($itemCode<>'') {
    if ($product_id==0) {
      $sql=$sql_tempate1." where gks_barcodes.barcode='".$db_link->escape_string($itemCode)."' and gks_barcodes.user_id=".$myinvoice['user_id'];
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $product_id=intval($row['product_id']);
        $product_descr=trim_gks($row['product_descr_p']);
        $product_comments=$product['itemDescr'];
      }
    }
    if ($product_id==0) {
      $sql=$sql_tempate1." where gks_barcodes.barcode='".$db_link->escape_string($itemCode)."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $product_id=intval($row['product_id']);
        $product_descr=trim_gks($row['product_descr_p']);
        $product_comments=$product['itemDescr'];
      }
    }
  }

  $itemDescr=$product['itemDescr'];
  if ($itemDescr<>'') {
    if ($product_id==0) {
      $sql=$sql_tempate2." where product_descr_p like '".$db_link->escape_string($itemDescr)."'";
      //echo '<pre>'.$sql;die();
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $product_id=intval($row['id_product']);
        $product_descr=trim_gks($row['product_descr_p']);
        $product_comments=$product['itemDescr'];
      }
    }
  }
    

  if ($product_id==0) $product_id=2;
  if ($product_descr=='') $product_descr=gks_lang('Είδος με αα').' '.$product['lineNumber'];
  
  if ($product_comments!='' and $aade_lineComments!='') {
    $product_comments.="\r\n".$aade_lineComments;
  } else if ($product_comments=='' and $aade_lineComments!='') {
    $product_comments=$aade_lineComments;
    //$aade_lineComments='';
  }
  if ($product_descr==$product_comments) $product_comments='';
  

  
  $sql="insert into ".$mytable."_products (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  ".$myidrtable.",product_aa,product_id,product_descr,
  product_monada_id_org,product_monada_id,
  product_quantity,";
  
  if ($myinvoice['invoiceType']!='9.3') {
  $sql.="product_fpa_base_id,
  product_fpa_id,
  product_fpa_pososto,
  
  product_price_check_fpa,
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
  ";
  }
  
  $sql.="
  product_comments,
  aade_lineComments,
  from_aade_import_lock,
  p_warehouses_id_from,
  p_warehouses_id_to
  
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$id.",".$product['lineNumber'].",".$product_id.",'".$db_link->escape_string($product_descr)."',
  ".$product_monada_id.",".$product_monada_id.",
  ".$product['quantity'].",";
  
  if ($myinvoice['invoiceType']!='9.3') {
  $sql.=$product_fpa_base_id.",
  ".$product_fpa_id.",
  ".number_format($product_fpa_pososto,2,'.','').",

  0,
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
  
  ".intval($product['vatExemptionCategory']).",";
  
  }
  
  $sql.="'".$db_link->escape_string($product_comments)."',
  '".$db_link->escape_string($aade_lineComments)."',
  1,
  ".$myinvoice['warehouses_id_from'].",
  ".$myinvoice['warehouses_id_to']."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
  $id_acc_inv_product = $db_link->insert_id;

  foreach ($product['incomeClassification'] as $xarak) {
  
    $aade_katigoria_xarakt_esodon_id=0;
    $aade_typos_xarakt_esodon_id=0;
    $acc_inv_product_income_ammount=$xarak['amount'];
    if ($xarak['classificationCategory']!='') {
      $sql="select id_aade_katigoria_xarakt_esodon from gks_aade_katigoria_xarakt_esodon 
      where aade_katigoria_xarakt_esodon_code like '".$db_link->escape_string($xarak['classificationCategory'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();      
        $aade_katigoria_xarakt_esodon_id=$row['id_aade_katigoria_xarakt_esodon'];
      }
    }
    if ($xarak['classificationType']!='') {
      $sql="select id_aade_typos_xarakt_esodon from gks_aade_typos_xarakt_esodon 
      where aade_typos_xarakt_esodon_code like '".$db_link->escape_string($xarak['classificationType'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
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
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }

  foreach ($product['expensesClassification'] as $xarak) {
  
    $aade_katigoria_xarakt_eksodon_id=0;
    $aade_typos_xarakt_eksodon_id=0;
    $acc_inv_product_expenses_ammount=$xarak['amount'];
    if ($xarak['classificationCategory']!='') {
      $sql="select id_aade_katigoria_xarakt_eksodon from gks_aade_katigoria_xarakt_eksodon 
      where aade_katigoria_xarakt_eksodon_code like '".$db_link->escape_string($xarak['classificationCategory'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();      
        $aade_katigoria_xarakt_eksodon_id=$row['id_aade_katigoria_xarakt_eksodon'];
      }
    }
    if ($xarak['classificationType']!='') {
      $sql="select id_aade_typos_xarakt_eksodon from gks_aade_typos_xarakt_eksodon 
      where aade_typos_xarakt_eksodon_code like '".$db_link->escape_string($xarak['classificationType'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
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
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }

} 

if ($myinvoice['invoiceType']=='9.3') {
$html_out='<a href="admin-whi-mov-item.php?id='.$id.'" class="alert-link">#'.$id.'</a>'.
        ' <span class="whi_mov_state_010draft">'.getWhiMovStateDescr('010draft').'</span>';  
  
} else {
$html_out='<a href="admin-acc-inv-item.php?id='.$id.'" class="alert-link">#'.$id.'</a>'.
        ' <span class="acc_inv_state_010draft">'.getAccInvStateDescr('010draft').'</span>';  
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'html_out' => base64_encode($html_out));
echo json_encode($return); die();


$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myinvoice,true)));
echo json_encode($return); die();


//400001830638514
//400001827643201