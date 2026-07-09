<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Αναζήτηση εγγράφων ΑΑΔΕ μέσω myData');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_aade_docs','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']),'html'=>base64_encode($perm_ret['message']));echo json_encode($return); die();}

$perm_acc_aade_docs_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_acc_aade_docs','edit',0);
$perm_acc_aade_docs_add =gks_permission_user_can_action_php($my_wp_user_id, 'gks_acc_aade_docs','add',0);

//echo '<pre>sssss';die();


$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0; if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
$operation=0; if (isset($_POST['operation'])) $operation=intval($_POST['operation']);
$mark=0; if (isset($_POST['mark'])) $mark=intval($_POST['mark']);
if ($mark>2) $mark=$mark-1;
$maxMark=0; if (isset($_POST['maxMark'])) $maxMark=intval($_POST['maxMark']);
$dateFrom=''; if (isset($_POST['dateFrom'])) $dateFrom=trim_gks($_POST['dateFrom']);
$dateTo=''; if (isset($_POST['dateTo'])) $dateTo=trim_gks($_POST['dateTo']);
$entityVatNumber=''; if (isset($_POST['entityVatNumber'])) $entityVatNumber=trim_gks($_POST['entityVatNumber']);
$receiverVatNumber=''; if (isset($_POST['receiverVatNumber'])) $receiverVatNumber=trim_gks($_POST['receiverVatNumber']);
$invType=''; if (isset($_POST['invType'])) $invType=trim_gks($_POST['invType']);
$GroupedPerDay=''; if (isset($_POST['GroupedPerDay'])) $GroupedPerDay=trim_gks($_POST['GroupedPerDay']);

$fileget='';if (isset($_POST['fileget'])) $fileget=trim_gks($_POST['fileget']);

$gks_customtableview_class=''; if (isset($_POST['gks_customtableview_class'])) $gks_customtableview_class=trim_gks($_POST['gks_customtableview_class']);

//echo '<pre>';echo  $company_id; die();

if ($fileget=='') {
  if ($company_id<=0) {debug_mail(false,'select company','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μια εταιρεία')));
      echo json_encode($return); die();}
  
  if ($operation!=0 and $operation!=1 and $operation!=2 and $operation!=3) {debug_mail(false,'select operation',gks_lang('Η λειτουργία δεν είναι σωστή').' (2)<br>'.gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η λειτουργία δεν είναι σωστή').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
      echo json_encode($return); die();}
  
  if ($mark<=0) {debug_mail(false,'mark is negative');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Το <b>Από ΜΑΡΚ</b> πρέπει να είναι αριθμός μεγαλύτερος του μηδέν')));
      echo json_encode($return); die();}
  
  if ($maxMark>0 and $maxMark<$mark) {debug_mail(false,'mark is negative');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Το <b>Έως ΜΑΡΚ</b> πρέπει να είναι μεγαλύτερο από το <b>Από ΜΑΡΚ</b> ή κενό')));
      echo json_encode($return); die();}
  
  $dateFrom_time=0; //   format dd/MM/yyyy
  if ($dateFrom!='') {
    $dateFrom_time=gks_myFormatDate($dateFrom);
    //echo '<pre>'.$dateFrom_time.' '.date('Y-m-d H:i:s',$dateFrom_time);die();
  }
  $dateTo_time=0; //   format dd/MM/yyyy
  if ($dateTo!='') {
    $dateTo_time=gks_myFormatDate($dateTo);
    //echo '<pre>'.$dateTo_time.' '.date('Y-m-d H:i:s',$dateTo_time);die();
  }
  //echo '<pre>'.$dateFrom.' '.$dateTo.' '.$dateFrom_time.' '.$dateTo_time;die();
  if ($dateFrom_time<>0 and $dateTo_time<>0 and $dateFrom_time > $dateTo_time) {
      debug_mail(false,'mark is negative');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η <b>Έως Ημερομηνία</b> πρέπει να είναι μεγαλύτερη ή ίση από το <b>Από Ημερομηνία</b> ή κενή')));
      echo json_encode($return); die();}
  
  
  
  
  //echo '<pre>'.$maxMark;print_r($_POST);die();




  
  if ($operation==0) {
    $prefix_filename='RequestTransmittedDocs';
    $ret = gks_aade_request_transmitted_docs($company_id,$company_sub_id,$mark,$maxMark,$dateFrom,$dateTo,$entityVatNumber,$receiverVatNumber,$invType);
  } else if ($operation==1) {
    $prefix_filename='RequestDocs';
    $ret = gks_aade_request_docs($company_id,$company_sub_id,$mark,$maxMark,$dateFrom,$dateTo,$entityVatNumber,$receiverVatNumber,$invType);
  } else if ($operation==2) {
    $prefix_filename='RequestVatInfo_'.$GroupedPerDay;
    $ret = gks_aade_requestvatinfo($company_id,$company_sub_id,$dateFrom,$dateTo,$entityVatNumber,$GroupedPerDay);
  } else if ($operation==3) {
    $prefix_filename='RequestE3Info_'.$GroupedPerDay;
    $ret = gks_aade_requeste3info($company_id,$company_sub_id,$dateFrom,$dateTo,$entityVatNumber,$GroupedPerDay);
  }
  
  
} else {
  $fullpath=GKS_SITE_PATH.'tmp/'.$fileget;
  if (file_exists($fullpath)==false) {
    $html='<div class="alert alert-danger" role="alert">'.gks_lang('Δεν βρέθηκε το απαραίτητο αρχείο').'<br>'.gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε').'</div>';
    $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
    echo json_encode($return); die();}    
  
  $fileget_run_options=$fullpath.'.json';
  if (file_exists($fileget_run_options)==false) {
    $html='<div class="alert alert-danger" role="alert">'.gks_lang('Δεν βρέθηκε το απαραίτητο αρχείο').' (options)<br>'.gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε').'</div>';
    $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
    echo json_encode($return); die();}    
  
  $fileget_run_options_data=json_decode(file_get_contents($fileget_run_options), true);
  //print '<pre>';print_r($fileget_run_options_data);die();
  
  $company_id = $fileget_run_options_data['company_id'];
  $company_sub_id = $fileget_run_options_data['company_sub_id'];
  $operation = $fileget_run_options_data['operation'];  
  
  //@touch($fullpath);
  $ret=array();
  $ret['out_xml']=file_get_contents($fullpath);
  $ret['message']='OK';
  $ret['success']=true;

  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$fileget));
  //echo json_encode($return); die();  
  
}


if ($ret['success']) {
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/aade_docs'.time().'.xml',$ret['out_xml']);
  //echo '<pre>ssss tmp/aade_docs.xml';die();

  if ($operation<=1) { //RequestDocs RequestTransmittedDocs
  
    $errors=array();
    $mylist=array();
    $fileget_run='';
    try {
      
      $xml = new SimpleXMLElement($ret['out_xml'], LIBXML_NOERROR);
      if (isset($xml->invoicesDoc)) {
        
        if ($fileget=='') {
          $fileget_run=$prefix_filename.'_'.$my_wp_user_id.'_'.showDate(time(),'YmdHis',1).'.xml';
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run,$ret['out_xml']);
          
          $fileget_run_options=$fileget_run.'.json';
          $fileget_run_options_data=array(
            'company_id' => $company_id,
            'company_sub_id' => $company_sub_id,
            'operation' => $operation,
          );
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run_options,json_encode($fileget_run_options_data));
        }
        $invoicesDoc=$xml->invoicesDoc;
        $i=0;
        foreach ($invoicesDoc->children() as $invoice) {
          $i++;
          $thisxml='<?xml version="1.0" encoding="utf-8"?>'."\n".
          '<RequestedDoc xmlns:icls="https://www.aade.gr/myDATA/incomeClassificaton/v1.0" xmlns:ecls="https://www.aade.gr/myDATA/expensesClassificaton/v1.0" xmlns="http://www.aade.gr/myDATA/invoice/v1.0">'."\n".
          '  <invoicesDoc>'."\n".
          '    '.
          $invoice->asXML()."\n".
          '  </invoicesDoc>'."\n".
          '</RequestedDoc>';
          if (isset($invoice->mark)) {
            $mark_doc=(string)$invoice->mark;
            $filename=$company_id.'_'.$company_sub_id.'_'.$operation.'_'.$mark_doc.'.xml';
            if ($fileget=='') {
              file_put_contents(GKS_SITE_PATH.'tmp/'.$filename,$thisxml);
            }
          } else {
            $errors[]=str_replace('[n]',gks_n_h($i),gks_lang('Δεν βρέθηκε το ΜΑΡΚ από την [n] καταχώρηση'));
            $mark_doc='';
            $filename='';
          }
          if (isset($invoice->issuer->vatNumber)) {
            $afm_issuer=(string)$invoice->issuer->vatNumber;
          } else {
            $afm_issuer='';
          }
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
          
          if (isset($invoice->invoiceSummary->totalNetValue)) {
            $totalNetValue=(string)$invoice->invoiceSummary->totalNetValue;
          } else {
            $totalNetValue='';
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
          
          if (isset($invoice->invoiceHeader->isDeliveryNote)) {
            $isDeliveryNote=(string)$invoice->invoiceHeader->isDeliveryNote;
            if ($isDeliveryNote=='1' or $isDeliveryNote=='true') {
              $isDeliveryNote_descr=' - '.gks_lang('Δελτίο Διακίνησης');
            } else if ($isDeliveryNote=='0' or $isDeliveryNote=='false') {
              
            } else {
              $isDeliveryNote_descr=$isDeliveryNote;
            }
          } else {
            $isDeliveryNote='';
            $isDeliveryNote_descr='';
          }   
          
          $mylist[]=array(
            'filename' => $filename,
            'mark' => $mark_doc,
            'afm_issuer' => $afm_issuer,
            'user_id_issuer' => 0,
            'afm_counterpart' => $afm_counterpart,
            'user_id_counterpart' => 0,
            'issueDate'=> $issueDate,
            'issueDateint'=> strtotime($issueDate),
            'totalNetValue'=>floatval($totalNetValue),
            'id_acc_inv' => 0,
            'id_whi_mov' => 0,
            'text'=> '',
            'invoiceType' => $invoiceType,
            'inv_type_descr' => '',
            'isDeliveryNote' => $isDeliveryNote,
            'isDeliveryNote_descr' => $isDeliveryNote_descr,
            'seira' => $seira,
            'number' => intval($my_aa),
          );
        } 
      } else {
        
        $errors[]=gks_lang('Δεν βρέθηκε το invoicesDoc στην αρχή του αρχείου XML');
      }  
      
    } catch (Exception $e) { 
      $errors[]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
    }
    
    if (count($errors)>0) {  
      $html='<div class="alert alert-danger" role="alert">'.gks_lang('Σφάλμα').': '.implode('<br>',$errors).'</div>';
      $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
      echo json_encode($return); die();}
  
    $_cache=time().rand(1000,9999).rand(1000,9999);
    if (count($mylist)==0) {
      $html='<div class="alert alert-info" role="alert">'.gks_lang('Δεν βρέθηκαν παραστατικά').'</div>';
    } else {
  
      $afms=array();
      foreach ($mylist as $item) {
        if ($item['afm_issuer']!='') {
          $afm="'".$db_link->escape_string($item['afm_issuer'])."'";
          if (in_array($afm,$afms)==false) $afms[]=$afm;
        }
        if ($item['afm_counterpart']!='') {
          $afm="'".$db_link->escape_string($item['afm_counterpart'])."'";
          if (in_array($afm,$afms)==false) $afms[]=$afm;
        }
      }
      $afms_company=array();
      $afms_user=array();
      if (count($afms)>0) {
        $sql="SELECT id_company, company_title, company_eponimia, company_afm 
        FROM gks_company 
        WHERE company_afm In (".implode(',',$afms).")
        order by id_company";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
  
        while ($row = $result->fetch_assoc()) {
          if (isset($afms_company[$row['company_afm']])==false) {
            $title=trim_gks($row['company_title']);
            if ($title=='') $title=trim_gks($row['company_eponimia']);
            if ($title=='') $title='id: '.$row['id_company'];
            $afms_company[$row['company_afm']]=array(
              'id' => $row['id_company'],
              'title' => $title,
            );
          }
        }
        
        
        $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, gks_users.eponimia, gks_users.title, gks_users.afm
        FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
        WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm In (".implode(',',$afms).")
        order by ".GKS_WP_TABLE_PREFIX."users.ID;";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
  
        while ($row = $result->fetch_assoc()) {
          if (isset($afms_user[$row['afm']])==false) {
            $title=trim_gks($row['title']);
            if ($title=='') $title=trim_gks($row['eponimia']);
            if ($title=='') $title='id: '.$row['ID'];
            
            $afms_user[$row['afm']]=array(
              'id' => $row['ID'],
              'title' => $title,
            );
          }
        }
        
        //print '<pre>';print_r($afms_user);die();
      }
      
      $sql="SELECT id_acc_eidos_parastatikou, eidos_parastatikou_descr, eidos_parastatikou_aade_code 
      FROM gks_acc_eidi_parastatikon 
      WHERE eidos_parastatikou_aade_code<>''";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      $eidos_parastatikou=array();
      while ($row = $result->fetch_assoc()) {
        
        if ($row['eidos_parastatikou_aade_code']=='9.3') $row['eidos_parastatikou_descr']=gks_lang('Δελτίο Αποστολής');
        $eidos_parastatikou[$row['eidos_parastatikou_aade_code']]=array(
          'id' => $row['id_acc_eidos_parastatikou'],
          'descr' => $row['eidos_parastatikou_descr'],
        );
      }    
      
  
  
      foreach ($mylist as &$item) {
        
        if (isset($eidos_parastatikou[$item['invoiceType']])) {
          $item['inv_type_descr']=$eidos_parastatikou[$item['invoiceType']]['descr'];
        }
        
        
        //print '<pre>';print_r($item);die();
        
        $sql="SELECT gks_acc_inv.id_acc_inv,gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
        gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
        gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
        gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
        gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
        gks_acc_inv.gks_price_net,gks_acc_inv.gks_price_fpa
        FROM (((gks_acc_inv 
        LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
        LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
        LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
        where aade_invoicemark='".$db_link->escape_string($item['mark'])."'";
        
        if ($operation==0) { //diko_mou
          $sql.=" and gks_acc_inv.company_id=".$company_id;
          //$sql.=" and gks_acc_inv.company_sub_id=".$company_sub_id;
        } else { //apo_allon
          $sql.=" and gks_acc_inv.company_id=".$company_id;
          //$sql.=" and gks_acc_inv.company_sub_id=".$company_sub_id;
        }
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        if ($result->num_rows>=1) {
          $row = $result->fetch_assoc();
          $item['id_acc_inv']=$row['id_acc_inv'];
          $item['text']=  '<a href="admin-acc-inv-item.php?id='.$row['id_acc_inv'].'" class="alert-link">#'.$row['id_acc_inv'].'</a>'.
          ' <span class="acc_inv_state_'.$row['inv_state'].'">'.getAccInvStateDescr($row['inv_state']).'</span>';  
          
        }
        
        if ($item['id_acc_inv']==0) {
          $sql="SELECT gks_whi_mov.id_whi_mov,gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
          gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
          gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
          gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
          gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
          gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
          gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_date,gks_whi_mov.mov_state
          
          FROM (((gks_whi_mov 
          LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
          LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
          LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
          LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
          where aade_invoicemark='".$db_link->escape_string($item['mark'])."'";          

          if ($operation==0) { //diko_mou
            $sql.=" and gks_whi_mov.company_id=".$company_id;
            //$sql.=" and gks_whi_mov.company_sub_id=".$company_sub_id;
          } else { //apo_allon
            $sql.=" and gks_whi_mov.company_id=".$company_id;
            //$sql.=" and gks_whi_mov.company_sub_id=".$company_sub_id;
          }
          
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); } 
          if ($result->num_rows>=1) {
            $row = $result->fetch_assoc();
            $item['id_whi_mov']=$row['id_whi_mov'];
            $item['text']=  '<a href="admin-whi-mov-item.php?id='.$row['id_whi_mov'].'" class="alert-link">#'.$row['id_whi_mov'].'</a>'.
            ' <span class="whi_mov_state_'.$row['mov_state'].'">'.getWhiMovStateDescr($row['mov_state']).'</span>';  
            
          }          
          
        }
        
        
         
      }
      unset($item);  
      
      $sort_field='mark';if (isset($_POST['sort_field'])) $sort_field=trim_gks($_POST['sort_field']);
      $sort_adesc='desc';if (isset($_POST['sort_adesc'])) $sort_adesc=trim_gks($_POST['sort_adesc']);
      
      if      ($sort_field=='mark'   and $sort_adesc=='asc')  usort($mylist, "vals_sort_mark_asc");
      else if ($sort_field=='mark'   and $sort_adesc=='desc') usort($mylist, "vals_sort_mark_desc");
      else if ($sort_field=='issuer' and $sort_adesc=='asc')  usort($mylist, "vals_sort_issuer_asc");
      else if ($sort_field=='issuer' and $sort_adesc=='desc') usort($mylist, "vals_sort_issuer_desc");
      else if ($sort_field=='part' and $sort_adesc=='asc')    usort($mylist, "vals_sort_part_asc");
      else if ($sort_field=='part' and $sort_adesc=='desc')   usort($mylist, "vals_sort_part_desc");
      else if ($sort_field=='date' and $sort_adesc=='asc')    usort($mylist, "vals_sort_date_asc");
      else if ($sort_field=='date' and $sort_adesc=='desc')   usort($mylist, "vals_sort_date_desc");
      else if ($sort_field=='netval' and $sort_adesc=='asc')  usort($mylist, "vals_sort_netval_asc");
      else if ($sort_field=='netval' and $sort_adesc=='desc') usort($mylist, "vals_sort_netval_desc");
      else if ($sort_field=='locid' and $sort_adesc=='asc')   usort($mylist, "vals_sort_locid_asc");
      else if ($sort_field=='locid' and $sort_adesc=='desc')  usort($mylist, "vals_sort_locid_desc");
      else if ($sort_field=='invtype' and $sort_adesc=='asc')   usort($mylist, "vals_sort_invtype_asc");
      else if ($sort_field=='invtype' and $sort_adesc=='desc')  usort($mylist, "vals_sort_invtype_desc");
      else if ($sort_field=='seira' and $sort_adesc=='asc')   usort($mylist, "vals_sort_seira_asc");
      else if ($sort_field=='seira' and $sort_adesc=='desc')  usort($mylist, "vals_sort_seira_desc");
      else if ($sort_field=='number' and $sort_adesc=='asc')   usort($mylist, "vals_sort_number_asc");
      else if ($sort_field=='number' and $sort_adesc=='desc')  usort($mylist, "vals_sort_number_desc");
      
      $html='';
      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable '.$gks_customtableview_class.'" style="font-size: 0.8rem;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
          '<tr>'.	
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>'.
              '<th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"  nowrap="nowrap"><span class="mysort" data-field="mark">'.gks_lang('ΜΑΡΚ').'</span>'.mysorticon('mark').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><span class="mysort" data-field="issuer">'.gks_lang('Εκδότης').'</span>'.mysorticon('issuer').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><span class="mysort" data-field="part">'.gks_lang('Λήπτης').'</span>'.mysorticon('part').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><span class="mysort" data-field="invtype">'.gks_lang('Τύπος').'</span>'.mysorticon('invtype').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><span class="mysort" data-field="seira">'.gks_lang('Σειρά').'</span>'.mysorticon('seira').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><span class="mysort" data-field="number">'.gks_lang('Αριθμός').'</span>'.mysorticon('number').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="date">'.gks_lang('Ημέρα').'</span>'.mysorticon('date').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: right  !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="netval">'.gks_lang('Ποσό').'</span>'.mysorticon('netval').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap" ><span class="tooltipster" title="'.gks_lang('Εισαγωγή barcodes ειδών').'">'.gks_lang('Barcodes').'</span></th>'.      
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  nowrap="nowrap"><span class="mysort" data-field="locid">'.gks_lang('Καταχώρηση').'</span>'.mysorticon('locid').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap">'.gks_lang('XML').'</th>'.      
          '</tr>'.
      '</thead>'.
      '<tbody>';  
      $i=0;
      foreach ($mylist as $item) {
        $i++;
        
        $afm_issuer=$item['afm_issuer'];
        if (isset($afms_company[$item['afm_issuer']])) 
          $afm_issuer.=' <a href="admin-company-item.php?id='.$afms_company[$item['afm_issuer']]['id'].'">'.$afms_company[$item['afm_issuer']]['title'].'</a>';
        else if (isset($afms_user[$item['afm_issuer']])) 
          $afm_issuer.=' <a href="admin-users-item.php?id='.$afms_user[$item['afm_issuer']]['id'].'">'.$afms_user[$item['afm_issuer']]['title'].'</a>';
        else if ($item['afm_issuer']!='')
          if ($perm_acc_aade_docs_edit) 
            $afm_issuer.=' <i class="fas fa-save user_create" data-val="'.$item['afm_issuer'].'" data-cus_sup="sup" title="'.gks_lang('Δημιουργία επαφής').'"></i>';
        
        $afm_counterpart=$item['afm_counterpart'];
        if (isset($afms_company[$item['afm_counterpart']])) 
          $afm_counterpart.=' <a href="admin-company-item.php?id='.$afms_company[$item['afm_counterpart']]['id'].'">'.$afms_company[$item['afm_counterpart']]['title'].'</a>';
        else if (isset($afms_user[$item['afm_counterpart']])) 
          $afm_counterpart.=' <a href="admin-users-item.php?id='.$afms_user[$item['afm_counterpart']]['id'].'">'.$afms_user[$item['afm_counterpart']]['title'].'</a>';
        else if ($item['afm_counterpart']!='')
          if ($perm_acc_aade_docs_edit) 
            $afm_counterpart.=' <i class="fas fa-save user_create" data-val="'.$item['afm_counterpart'].'" data-cus_sup="cus" title="'.gks_lang('Δημιουργία επαφής').'"></i>';
        
        
        
        
        $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">'.
        '<th scope="row" nowrap class="mytdcm aa">'.$i.'</th>'.
        '<td class="mytdcmr" nowrap>'.$item['mark'].'</td>'.
        '<td class="mytdcml">'.$afm_issuer.'</td>'.
        '<td class="mytdcml">'.$afm_counterpart.'</td>'.
        '<td class="mytdcml">'.$item['inv_type_descr'].$item['isDeliveryNote_descr'].'</td>'.
        '<td class="mytdcm">'.$item['seira'].'</td>'.
        '<td class="mytdcm">'.$item['number'].'</td>'.
        '<td class="mytdcm"  nowrap>'.date('d/m/Y',$item['issueDateint']).'</td>'.
        '<td class="mytdcmr" nowrap>'.myCurrencyFormat($item['totalNetValue']).'</td>'.
        '<td class="mytdcm" nowrap><a href="admin-acc-aade-docs-codes.php?fs=tmp&file='.rawurlencode($item['filename']).'&_cache='.$_cache.'" target="_blank"><i class="fas fa-file-import" style="font-size:150%;"></i></a></td>'.
        '<td class="mytdcml">'.$item['text'].
        (($item['id_acc_inv'] == 0 and $item['id_whi_mov'] == 0) ? 
          ($perm_acc_aade_docs_add ? '<button type="button" class="btn btn-sm btn-primary btn-sm button_add" data-mark="'.$company_id.'_'.$company_sub_id.'_'.$operation.'_'.$item['mark'].'">'.gks_lang('Προσθήκη').' ...</button>' : '')
          : '').
        '</td>'.
        '<td class="mytdcm" nowrap><a href="admin-get-file.php?fs=tmp&file='.rawurlencode($item['filename']).'&_cache='.$_cache.'" target="_blank"><i class="fas fa-download" style="font-size:150%;"></i></a></td>'.
        '</tr>';
        
      }
      
      $html.='</tbody>
      </table>';
    
      
    }
  }
  
  if ($operation==2) { //RequestVatInfo
    $errors=array();
    $mylist=array();
    $fileget_run='';
    
    try {
      
      $xml = new SimpleXMLElement($ret['out_xml'], LIBXML_NOERROR);
      $xmlname=$xml->getName();
      
      if ($xmlname=='RequestedVatInfo') {
        
        if ($fileget=='') {
          $fileget_run=$prefix_filename.'_'.$my_wp_user_id.'_'.showDate(time(),'YmdHis',1).'.xml';
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run,$ret['out_xml']);
          
          $fileget_run_options=$fileget_run.'.json';
          $fileget_run_options_data=array(
            'company_id' => $company_id,
            'company_sub_id' => $company_sub_id,
            'operation' => $operation,
            'GroupedPerDay' => $GroupedPerDay,
          );
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run_options,json_encode($fileget_run_options_data));
        }
        $RequestedVatInfo=$xml->RequestedVatInfo;
        $i=0;
        
        $marks_array=[];
        foreach ($xml->children() as $vatitem) {
          $issueDate='';
          $Mark='';
          $IsCancelled='';
          $vats=[];
          $vat=0;
          foreach ($vatitem->children() as $vim) {
            $iname=$vim->getName();
            if ($iname=='IssueDate') {
              $issueDate=(string)$vim;
            } else if (substr($iname,0,3)=='Vat') {
              $vats[]=array($iname,floatval((string)$vim));
              $vat+=floatval((string)$vim);
            } else if ($iname=='Mark') {
              $Mark=(string)$vim;
            } else if ($iname=='IsCancelled') {
              $IsCancelled=(string)$vim;
            }  
          }
          
          //echo '<pre>'.$iname.' '.$issueDate.' '.$Mark.' '.$IsCancelled.' '.print_r($vats,true);die();
          //echo '<pre>';var_dump($vatitem);die();
                   

          $mylist[]=array(
            'issueDate' => $issueDate,
            'issueDateint'=> strtotime($issueDate),
            'mark' => $Mark,
            'cancelled' => $IsCancelled,
            'cancelledint' => ($IsCancelled=='true' ? 1 : 0),
            'vats' => $vats,
            'vat' => $vat,
            'id_acc_inv'=>0,
          );
          
          if ($Mark!='') {
            $fMark="'".$db_link->escape_string($Mark)."'";
            if (in_array($fMark,$marks_array)==false) $marks_array[]= $fMark;
          }
        }
        
        if (count($marks_array)>0) {
          $sql="select id_acc_inv,aade_invoicemark from gks_acc_inv where aade_invoicemark in (".implode(',',$marks_array).")";
          //echo $sql;die();
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }           
          
          while ($row = $result->fetch_assoc()) {
            foreach ($mylist as &$value) {
              //print '<pre>';print_r($row);print_r($value);die();
              if ($value['mark']==$row['aade_invoicemark']) {
                $value['id_acc_inv']=$row['id_acc_inv'];
                break;
              }
            }   
            unset($value);
          }  
          
        }
        //echo '<pre>sssss '.print_r($mylist);die();
      } else {
        
        $errors[]=gks_lang('Δεν βρέθηκε το RequestedVatInfo στην αρχή του αρχείου XML');
      }  
      
    } catch (Exception $e) { 
      $errors[]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
    }

    if (count($errors)>0) {  
      $html='<div class="alert alert-danger" role="alert">'.gks_lang('Σφάλμα').': '.implode('<br>',$errors).'</div>';
      $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
      echo json_encode($return); die();}

    if (count($mylist)==0) {
      $html='<div class="alert alert-info" role="alert">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</div>';
    } else {
      //$html='<pre>'.print_r($mylist,true).'</pre>';
      
      $sort_field='date';if (isset($_POST['sort_field'])) $sort_field=trim_gks($_POST['sort_field']);
      $sort_adesc='desc';if (isset($_POST['sort_adesc'])) $sort_adesc=trim_gks($_POST['sort_adesc']);
      
      if      ($sort_field=='date' and $sort_adesc=='asc')    usort($mylist, "vals_sort_date_asc");
      else if ($sort_field=='date' and $sort_adesc=='desc')   usort($mylist, "vals_sort_date_desc");
      else if ($sort_field=='mark'   and $sort_adesc=='asc')  usort($mylist, "vals_sort_mark_asc");
      else if ($sort_field=='mark'   and $sort_adesc=='desc') usort($mylist, "vals_sort_mark_desc");
      else if ($sort_field=='cancelled' and $sort_adesc=='asc')  usort($mylist, "vals_sort_cancelled_asc");
      else if ($sort_field=='cancelled' and $sort_adesc=='desc') usort($mylist, "vals_sort_cancelled_desc");
      else if ($sort_field=='vat' and $sort_adesc=='asc')    usort($mylist, "vals_sort_vat_asc");
      else if ($sort_field=='vat' and $sort_adesc=='desc')   usort($mylist, "vals_sort_vat_desc");

      
      $html='';
      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable '.$gks_customtableview_class.'" style="font-size: 0.8rem;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
          '<tr>'.	
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><span class="mysort" data-field="date">'.gks_lang('Ημέρα').'</span>'.mysorticon('date').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="mark">'.gks_lang('ΜΑΡΚ').'</span>'.mysorticon('mark').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="cancelled">'.gks_lang('Ακυρώθηκε').'</span>'.mysorticon('cancelled').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="60%"  nowrap="nowrap">'.gks_lang('ΦΠΑς').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: right  !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="vat">'.gks_lang('Σύνολο').'</span>'.mysorticon('vat').'</th>'.
          '</tr>'.
      '</thead>'.
      '<tbody>';  
      $i=0;
      foreach ($mylist as $item) {
        $i++;
        
        $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">'.
        '<th scope="row" nowrap class="mytdcm aa">'.$i.'</th>'.
        '<td class="mytdcm" nowrap>'.date('d/m/Y',$item['issueDateint']).'</td>'.
        '<td class="mytdcm" nowrap>';
        if ($item['id_acc_inv']>0) {
          $html.='<a href="admin-acc-inv-item.php?id='.$item['id_acc_inv'].'">'.$item['mark'].'</a>';
        } else {
          $html.=$item['mark'];
        }
        
        $html.='</td>'.
        '<td class="mytdcm">';
        if ($item['cancelled']=='true') $html.=gks_lang('Ναι');
        else if ($item['cancelled']=='false') $html.=gks_lang('Όχι');
        $html.='</td>'.
        '<td class="mytdcml">';
        foreach ($item['vats'] as $vim) {
           $html.='<span class="vatitem1">'.$vim[0].': '.myCurrencyFormat($vim[1]).'</span>';
        }
        $html.='</td>'.
        '<td class="mytdcmr" nowrap>'.myCurrencyFormat($item['vat']).'</td>'.
        '</tr>';
      }
      
      $html.='</tbody>
      </table>';      
      
      $vattable=[];
      foreach ($mylist as $item) {
        foreach ($item['vats'] as $vim) {
          if (isset($vattable[$vim[0]])==false) $vattable[$vim[0]]=0;
          $vattable[$vim[0]]+=$vim[1];
        }
      }
      ksort($vattable);
      $html.='<h2 style="text-align:center;">'.gks_lang('Σύνολα').'</h2>';

      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" style="font-size: 0.8rem;width:1%" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
          '<tr>'.	
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap">'.gks_lang('ΦΠΑ').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap">'.gks_lang('Αξία').'</th>'.
          '</tr>'.
      '</thead>'.
      '<tbody>'; 
      $i=0;
      foreach ($vattable as $dd => $value) {
        $i++;
        $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">'.
        '<th scope="row" nowrap class="mytdcm aa">'.$i.'</th>'.
        '<td class="mytdcm" nowrap>'.$dd.'</td>'.
        '<td class="mytdcmr" nowrap>'.myCurrencyFormat($value).'</td>'.
        '</tr>';
        
      }       

      $html.='</tbody>
      </table>';
              
      //$html.='<pre>'.print_r($vattable,true).'</pre>';
      
    }
          
    //$html='dfgd '.print_r($errors,true);    
    
  }

  if ($operation==3) { //RequestE3Info
    $errors=array();
    $mylist=array();
    $fileget_run='';
    
    try {
      
      $xml = new SimpleXMLElement($ret['out_xml'], LIBXML_NOERROR);
      $xmlname=$xml->getName();
      
      if ($xmlname=='RequestedE3Info') {
        
        if ($fileget=='') {
          $fileget_run=$prefix_filename.'_'.$my_wp_user_id.'_'.showDate(time(),'YmdHis',1).'.xml';
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run,$ret['out_xml']);
          
          $fileget_run_options=$fileget_run.'.json';
          $fileget_run_options_data=array(
            'company_id' => $company_id,
            'company_sub_id' => $company_sub_id,
            'operation' => $operation,
            'GroupedPerDay' => $GroupedPerDay,
          );
          file_put_contents(GKS_SITE_PATH.'tmp/'.$fileget_run_options,json_encode($fileget_run_options_data));
        }
        $RequestE3Info=$xml->RequestE3Info;
        $i=0;
        //print '<pre>aaaa '.$ret['out_xml'];die();
        
        $marks_array=[];
        foreach ($xml->children() as $vatitem) {
          $V_Afm='';
          $V_Mark='';
          $vBook='';
          $IsCancelled='';
          $IssueDate='';
          $V_Class_Category='';
          $V_Class_Type='';
          $V_Class_Value='';
          
          foreach ($vatitem->children() as $vim) {
            $iname=$vim->getName();
            if ($iname=='V_Afm') {
              $V_Afm=(string)$vim;
            } else if ($iname=='V_Mark') {
              $V_Mark=(string)$vim;
            } else if ($iname=='vBook') {
              $vBook=(string)$vim;
            } else if ($iname=='IsCancelled') {
              $IsCancelled=(string)$vim;
            } else if ($iname=='IssueDate') {
              $IssueDate=(string)$vim;
            } else if ($iname=='V_Class_Category') {
              $V_Class_Category=(string)$vim;
            } else if ($iname=='V_Class_Type') {
              $V_Class_Type=(string)$vim;
            } else if ($iname=='V_Class_Value') {
              $V_Class_Value=floatval((string)$vim);
            } 
          }
          
          //echo '<pre>'.$iname.' '.$issueDate.' '.$Mark.' '.$IsCancelled.' '.print_r($vats,true);die();
          //echo '<pre>';var_dump($vatitem);die();
                   

          $mylist[]=array(
            'afm'=>$V_Afm,
            'mark' => $V_Mark,
            'vbook' => $vBook,
            'cancelled' => $IsCancelled,
            'cancelledint' => ($IsCancelled=='true' ? 1 : 0),
            'issueDate' => $IssueDate,
            'issueDateint'=> strtotime($IssueDate),
            'classcategory' => $V_Class_Category,
            'classtype' => $V_Class_Type,
            'classvalue' => $V_Class_Value,
            'id_acc_inv' => 0,
          );
          
          //print '<pre>aaaa ';print_r($mylist);die();
          
          if ($V_Mark!='') {
            $fMark="'".$db_link->escape_string($V_Mark)."'";
            if (in_array($fMark,$marks_array)==false) $marks_array[]= $fMark;
          }
        }
        
        if (count($marks_array)>0) {
          $sql="select id_acc_inv,aade_invoicemark from gks_acc_inv where aade_invoicemark in (".implode(',',$marks_array).")";
          //echo $sql;die();
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }           
          
          while ($row = $result->fetch_assoc()) {
            foreach ($mylist as &$value) {
              //print '<pre>';print_r($row);print_r($value);die();
              if ($value['mark']==$row['aade_invoicemark']) {
                $value['id_acc_inv']=$row['id_acc_inv'];
                break;
              }
            }   
            unset($value);
          }  
          
        }
        //echo '<pre>sssss '.print_r($mylist);die();
      } else {
        
        $errors[]=gks_lang('Δεν βρέθηκε το RequestE3Info στην αρχή του αρχείου XML');
      }  
      
    } catch (Exception $e) { 
      $errors[]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
    }

    if (count($errors)>0) {  
      $html='<div class="alert alert-danger" role="alert">'.gks_lang('Σφάλμα').': '.implode('<br>',$errors).'</div>';
      $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
      echo json_encode($return); die();}

    if (count($mylist)==0) {
      $html='<div class="alert alert-info" role="alert">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</div>';
    } else {
      //$html='<pre>'.print_r($mylist,true).'</pre>';
      
      $sort_field='date';if (isset($_POST['sort_field'])) $sort_field=trim_gks($_POST['sort_field']);
      $sort_adesc='desc';if (isset($_POST['sort_adesc'])) $sort_adesc=trim_gks($_POST['sort_adesc']);
      
      if      ($sort_field=='date' and $sort_adesc=='asc')    usort($mylist, "vals_sort_date_asc");
      else if ($sort_field=='date' and $sort_adesc=='desc')   usort($mylist, "vals_sort_date_desc");
      else if ($sort_field=='mark'   and $sort_adesc=='asc')  usort($mylist, "vals_sort_mark_asc");
      else if ($sort_field=='mark'   and $sort_adesc=='desc') usort($mylist, "vals_sort_mark_desc");
      else if ($sort_field=='afm'   and $sort_adesc=='asc')  usort($mylist, "vals_sort_afm_asc");
      else if ($sort_field=='afm'   and $sort_adesc=='desc') usort($mylist, "vals_sort_afm_desc");
      else if ($sort_field=='vbook'   and $sort_adesc=='asc')  usort($mylist, "vals_sort_vbook_asc");
      else if ($sort_field=='vbook'   and $sort_adesc=='desc') usort($mylist, "vals_sort_vbook_desc");
      else if ($sort_field=='cancelled' and $sort_adesc=='asc')  usort($mylist, "vals_sort_cancelled_asc");
      else if ($sort_field=='cancelled' and $sort_adesc=='desc') usort($mylist, "vals_sort_cancelled_desc");
      else if ($sort_field=='classcategory' and $sort_adesc=='asc')    usort($mylist, "vals_sort_classcategory_asc");
      else if ($sort_field=='classcategory' and $sort_adesc=='desc')   usort($mylist, "vals_sort_classcategory_desc");
      else if ($sort_field=='classtype' and $sort_adesc=='asc')    usort($mylist, "vals_sort_classtype_asc");
      else if ($sort_field=='classtype' and $sort_adesc=='desc')   usort($mylist, "vals_sort_classtype_desc");
      else if ($sort_field=='classvalue' and $sort_adesc=='asc')    usort($mylist, "vals_sort_classvalue_asc");
      else if ($sort_field=='classvalue' and $sort_adesc=='desc')   usort($mylist, "vals_sort_classvalue_desc");

      
      $html='';
      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable '.$gks_customtableview_class.'" style="font-size: 0.8rem;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
          '<tr>'.	
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap="nowrap"><span class="mysort" data-field="date">'.gks_lang('Ημέρα').'</span>'.mysorticon('date').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap="nowrap"><span class="mysort" data-field="afm">'.gks_lang('ΑΦΜ').'</span>'.mysorticon('afm').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="mark">'.gks_lang('ΜΑΡΚ').'</span>'.mysorticon('mark').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="vbook">'.gks_lang('vBook').'</span>'.mysorticon('vbook').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="cancelled">'.gks_lang('Ακυρώθηκε').'</span>'.mysorticon('cancelled').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap="nowrap"><span class="mysort" data-field="classcategory">'.gks_lang('Κατηγορία').'</span>'.mysorticon('classcategory').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap="nowrap"><span class="mysort" data-field="classtype">'.gks_lang('Τύπος').'</span>'.mysorticon('classtype').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: right  !important;" width="10%"  nowrap="nowrap"><span class="mysort" data-field="classvalue">'.gks_lang('Αξία').'</span>'.mysorticon('classvalue').'</th>'.
          '</tr>'.
      '</thead>'.
      '<tbody>';  
      $i=0;
      foreach ($mylist as $item) {
        $i++;
        
        $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">'.
        '<th scope="row" nowrap class="mytdcm aa">'.$i.'</th>'.
        '<td class="mytdcm" nowrap>'.date('d/m/Y',$item['issueDateint']).'</td>'.
        '<td class="mytdcm" nowrap>'.$item['afm'].'</td>'.
        '<td class="mytdcm" nowrap>';
        if ($item['id_acc_inv']>0) {
          $html.='<a href="admin-acc-inv-item.php?id='.$item['id_acc_inv'].'">'.$item['mark'].'</a>';
        } else {
          $html.=$item['mark'];
        }
        $html.='</td>'.
        '<td class="mytdcm" nowrap>'.$item['vbook'].'</td>'.
        '<td class="mytdcm">';
        if ($item['cancelled']=='true') $html.=gks_lang('Ναι');
        else if ($item['cancelled']=='false') $html.=gks_lang('Όχι');
        $html.='</td>'.
        '<td class="mytdcm" nowrap>'.$item['classcategory'].'</td>'.
        '<td class="mytdcm" nowrap>'.$item['classtype'].'</td>'.
        '<td class="mytdcmr" nowrap>'.myCurrencyFormat($item['classvalue']).'</td>'.
        '</tr>';
      }
      
      $html.='</tbody>
      </table>';      
      
      if (1==2) {
      $vattable=[];
      foreach ($mylist as $item) {
        foreach ($item['vats'] as $vim) {
          if (isset($vattable[$vim[0]])==false) $vattable[$vim[0]]=0;
          $vattable[$vim[0]]+=$vim[1];
        }
      }
      ksort($vattable);
      $html.='<h2 style="text-align:center;">'.gks_lang('Σύνολα').'</h2>';

      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" style="font-size: 0.8rem;width:1%" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
          '<tr>'.	
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap">'.gks_lang('ΦΠΑ').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap">'.gks_lang('Αξία').'</th>'.
          '</tr>'.
      '</thead>'.
      '<tbody>'; 
      $i=0;
      foreach ($vattable as $dd => $value) {
        $i++;
        $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">'.
        '<th scope="row" nowrap class="mytdcm aa">'.$i.'</th>'.
        '<td class="mytdcm" nowrap>'.$dd.'</td>'.
        '<td class="mytdcmr" nowrap>'.myCurrencyFormat($value).'</td>'.
        '</tr>';
        
      }       

      $html.='</tbody>
      </table>';
      }
              
      //$html.='<pre>'.print_r($vattable,true).'</pre>';
      
    }
          
    //$html='dfgd '.print_r($errors,true);    
    
  }  
  //$html='<pre>'.print_r($mylist,true).'</pre>';
  
  
  $return = array('success' => true, 'message' => base64_encode('ok'),'fileget' => ($fileget=='' ? $fileget_run : ''), 'html'=>base64_encode($html));
  echo json_encode($return); die();
  
  
} else {
  $html='<div class="alert alert-danger" role="alert">'.gks_lang('Σφάλμα').': '.$ret['message'].'</div>';
  $return = array('success' => true, 'message' => base64_encode('OK'), 'fileget' => '', 'html'=>base64_encode($html));
  echo json_encode($return); die();
}


$return = array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα')), 'fileget' => '');
echo json_encode($return); die();

function mysorticon($f) {
  global $sort_field;
  global $sort_adesc;
  if ($f!=$sort_field) return '';
  if ($sort_adesc=='asc') return '<img src="img/asc.png">';
  return '<img src="img/desc.png">';
  
}

function vals_sort_mark_asc($a, $b) {
  if ($a['mark'] > $b['mark']) return 1;
  if ($a['mark'] < $b['mark']) return -1;
  return 0;
}
function vals_sort_mark_desc($a, $b) {
  if ($a['mark'] > $b['mark']) return -1;
  if ($a['mark'] < $b['mark']) return 1;
  return 0;
}
function vals_sort_issuer_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['afm_issuer'], $b['afm_issuer']);
}
function vals_sort_issuer_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['afm_issuer'], $a['afm_issuer']);
}
function vals_sort_part_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['afm_counterpart'], $b['afm_counterpart']);
}
function vals_sort_part_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['afm_counterpart'], $a['afm_counterpart']);
}
function vals_sort_date_asc($a, $b) {
  if ($a['issueDateint'] > $b['issueDateint']) return 1;
  if ($a['issueDateint'] < $b['issueDateint']) return -1;
  return 0;
}
function vals_sort_date_desc($a, $b) {
  if ($a['issueDateint'] > $b['issueDateint']) return -1;
  if ($a['issueDateint'] < $b['issueDateint']) return 1;
  return 0;
}
function vals_sort_netval_asc($a, $b) {
  if ($a['totalNetValue'] > $b['totalNetValue']) return 1;
  if ($a['totalNetValue'] < $b['totalNetValue']) return -1;
  return 0;
}
function vals_sort_netval_desc($a, $b) {
  if ($a['totalNetValue'] > $b['totalNetValue']) return -1;
  if ($a['totalNetValue'] < $b['totalNetValue']) return 1;
  return 0;
}

function vals_sort_locid_asc($a, $b) {
  if ($a['id_acc_inv'] > $b['id_acc_inv']) return 1;
  if ($a['id_acc_inv'] < $b['id_acc_inv']) return -1;
  return 0;
}
function vals_sort_locid_desc($a, $b) {
  if ($a['id_acc_inv'] > $b['id_acc_inv']) return -1;
  if ($a['id_acc_inv'] < $b['id_acc_inv']) return 1;
  return 0;
}
function vals_sort_invtype_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['inv_type_descr'], $b['inv_type_descr']);
}
function vals_sort_invtype_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['inv_type_descr'], $a['inv_type_descr']);
}

function vals_sort_seira_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['seira'], $b['seira']);
}
function vals_sort_seira_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['seira'], $a['seira']);
}

function vals_sort_number_asc($a, $b) {
  if ($a['number'] > $b['number']) return 1;
  if ($a['number'] < $b['number']) return -1;
  return 0;
}
function vals_sort_number_desc($a, $b) {
  if ($a['number'] > $b['number']) return -1;
  if ($a['number'] < $b['number']) return 1;
  return 0;
}

function vals_sort_vat_asc($a, $b) {
  if ($a['vat'] > $b['vat']) return 1;
  if ($a['vat'] < $b['vat']) return -1;
  return 0;
}
function vals_sort_vat_desc($a, $b) {
  if ($a['vat'] > $b['vat']) return -1;
  if ($a['vat'] < $b['vat']) return 1;
  return 0;
}

function vals_sort_cancelled_asc($a, $b) {
  if ($a['cancelledint'] > $b['cancelledint']) return 1;
  if ($a['cancelledint'] < $b['cancelledint']) return -1;
  return 0;
}
function vals_sort_cancelled_desc($a, $b) {
  if ($a['cancelledint'] > $b['cancelledint']) return -1;
  if ($a['cancelledint'] < $b['cancelledint']) return 1;
  return 0;
}


function vals_sort_afm_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['afm'], $b['afm']);
}
function vals_sort_afm_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['afm'], $a['afm']);
}
function vals_sort_vbook_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['vbook'], $b['vbook']);
}
function vals_sort_vbook_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['vbook'], $a['vbook']);
}
function vals_sort_classcategory_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['classcategory'], $b['classcategory']);
}
function vals_sort_classcategory_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['classcategory'], $a['classcategory']);
}
function vals_sort_classtype_asc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['classtype'], $b['classtype']);
}
function vals_sort_classtype_desc($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($b['classtype'], $a['classtype']);
}
function vals_sort_classvalue_asc($a, $b) {
  if ($a['classvalue'] > $b['classvalue']) return 1;
  if ($a['classvalue'] < $b['classvalue']) return -1;
  return 0;
}
function vals_sort_classvalue_desc($a, $b) {
  if ($a['classvalue'] > $b['classvalue']) return -1;
  if ($a['classvalue'] < $b['classvalue']) return 1;
  return 0;
}
