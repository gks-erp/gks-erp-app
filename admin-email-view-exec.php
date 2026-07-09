<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$headers=0; if (isset($_GET['headers'])) $headers=intval($_GET['headers']);


$myid=0; if (isset($_GET['id'])) $myid=intval($_GET['id']);
if ($myid<=0) {
  debug_mail(false,'id is not set','');
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();
  } else {
    die(gks_lang('Δεν έχει ορισθεί το').' ID.');
  }
}  

$my_page_title=gks_lang('Προβολή απεσταλμένου email').' id:'.$myid.' headers='.$headers;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email','view',$myid);
if ($headers==1) {
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
} else {
  if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
}



$sql="select gks_email.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname 
FROM gks_email LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_email.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where gks_email.id=".$myid;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode('SQL Error'));
    echo json_encode($return); die();
  } else {
    die('SQL Error');
  }
}
if ($result->num_rows <= 0) {
  debug_mail(false,'email record not found',$sql);
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή')));
    echo json_encode($return); die();
  } else {
    die(gks_lang('Δεν βρέθηκε η εγγραφή'));
  }
}


$row = $result->fetch_assoc();  



if ($headers==1) {
  $html='
  <h5 align="center" style="padding-top:0px;">email</h5>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Από').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.$row['myfrom'].'</span>
          </div>
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Προς').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.$row['myto'].'</span>
          </div>';

  $mycc=trim_gks($row['mycc']);
  if ($mycc!='') {
    $vals= @json_decode($mycc,true);
    if (is_array($vals)) {
      $temp=array();
      foreach ($vals as $val) {
        if (is_array($val) and isset($val[0]) and trim_gks($val[0])!='') $temp[]=trim_gks($val[0]);
      } 
      if (count($temp)>0) {
        $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">CC:</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.implode(', ',$temp).'</span>
          </div>';
      }
    }
  }          
          
  $mybcc=trim_gks($row['mybcc']);
  if ($mybcc!='') {
    $vals= @json_decode($mybcc,true);
    if (is_array($vals)) {
      $temp=array();
      foreach ($vals as $val) {
        if (is_array($val) and isset($val[0]) and trim_gks($val[0])!='') $temp[]=trim_gks($val[0]);
      } 
      if (count($temp)>0) {
        $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">BCC:</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.implode(', ',$temp).'</span>
          </div>';
      }
    }
  }          
  
  
          
  $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Θέμα').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;font-weight: bold;">'.$row['subject'].'</span>
          </div>';
          
  $attachments=trim_gks($row['Attachments']);
  if ($attachments!='') {
    $vals= @json_decode($attachments,true);
    if (is_array($vals)) {
      $temp=array();
      //$temp[]='<pre>'.print_r($vals,true).'</pre>';
      foreach ($vals as $val) {
        if (is_array($val) and isset($val[1]) and trim_gks($val[1])!='') $temp[]=trim_gks($val[1]);
      } 
      if (count($temp)>0) {
        $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Συνημμένα').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.implode(', ',$temp).'</span>
          </div>';
      }
    }
  }          

  $html.='
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Ημερομηνία').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm">'.showDate(strtotime($row['date_add']), 'd/m/Y H:i',1).'</span>
          </div>
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('ID').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm">'.$myid.'</span>
          </div>';
  if ($row['user_id']>0) {
    $temp=trim_gks($row['gks_nickname']);
    if ($temp=='') $temp='User ID: '.$row['user_id'];
    $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Χρήστης').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.$temp.'</span>
          </div>';
  }
  if (trim_gks($row['model'])!='') {
    $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">Model:</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm">'.$row['model'].' '.$row['model_id'].'</span>
          </div>';
  }       
  $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Αποτέλεσμα αποστολής').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm"><img src="img/'.$row['myret'].'.png" border="0" width="16"></span>
          </div>';
          
  if ($row['views_count']>0) {          
    $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('Προβολές').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.myNumberFormat($row['views_count'],0).'</span>
          </div>';
  }          
  if (trim_gks($row['views_ips'])!='') {          
    $html.='
          <label class="col-md-4 col-form-label form-control-sm text-md-right">'.gks_lang('IPs Προβολής').':</label>
          <div class="col-md-8">
            <span class="form-control-plaintext form-control-sm" style="height: unset;">'.nl2br_gks($row['views_ips']).'</span>
          </div>';
  }          
  $html.='
        </div>
      </div>
    </div>';
  

  
  
  $html.='
    <div class="row">
      <div class="col-md-12">
        <iframe src="" style="width:100%;height:400px" id="dialog_email_iframe"></iframe>
      </div>
    </div>
  </div>';

  
  $return = array('success' => true, 'message' => base64_encode('OK'), 'html'=> base64_encode($html));
  echo json_encode($return); die();    
} else {
  $mybody = $row['body'];
  
  $mybody=str_replace('cid:my_img_logo200.png','/my/_current/_img_site/logo200.png',$mybody);
  $mybody=str_replace('cid:my_img_logo100.png','/my/_current/_img_site/logo100.png',$mybody);
  $mybody=str_replace('cid:my_img_','/my/img/',$mybody);
  
  $mybody=str_replace(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/','/',$mybody);
  $mybody=str_replace(GKS_SITE_PATH.'files/','about:blank?',$mybody);
  
  echo $mybody;
  die();
}



