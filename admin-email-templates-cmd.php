<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$cmd='';if (isset($_POST['cmd'])) $cmd=trim($_POST['cmd']);
if ($cmd=='' and $cmd!='getparams') {
  debug_mail(false,'the cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' cmd.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Εντολή Πρότυπου email');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email_template','autocomplete',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}








$sql="select * from gks_email_template where id_email_template=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}

$row = $result->fetch_assoc();

if ($cmd=='getparams') {
  $email_params='<table class="table table-sm table-responsive table-striped table-bordered" border="0" style="width:100%" cellspacing="0" cellpadding="5" align="center">'.
  '<thead>'.
    '<tr>'.
      '<th class="table-dark" scope="col" style="text-align: left  !important;" width="0%"  >'.gks_lang('Παράμετρος').':</th>'.
      '<th class="table-dark" scope="col" style="text-align: left  !important;" width="60%" >'.gks_lang('Τιμή','part2').'</th>'. 
      '<th class="table-dark" scope="col" style="text-align: left  !important;" width="40%" >'.gks_lang('π.χ.').'</th>'.
    '</tr>'.
  '</thead>'.
  '<tbody>'.
    '<tr>'.
      '<th scope="row" align="right">message:</td>'.  
      '<td colspan="2">'.gks_lang('Το κείμενο του παραπάνω πλαισίου κειμένου').'</td>'. 
    '</tr>';


  $params=[];
  $row['other_fields']=trim_gks($row['other_fields']);
  if ($row['other_fields']!='') {
    $params=json_decode($row['other_fields'],true);
  }
  foreach ($params as $vparam) {
    $email_params.='<tr>'.
    '<th scope="row" align="right">'.$vparam['label'].':'.
      (isset($vparam['icon']) ? '<br/>'.$vparam['icon'] : '').
    '</th>'.
    '<td>';
    $jquery_selector=''; 
    if (isset($vparam['jquery_selector'])) $jquery_selector=$vparam['jquery_selector'];
    
    if ($vparam['type']=='text') {
      $email_params.='<input id="'.$vparam['id'].'" type="'.$vparam['type'].'" class="form-control form-control-sm" data-jqs="'.base64_encode($jquery_selector).'" value="';
      if (isset($vparam['value'])) $email_params.=$vparam['value'];
      $email_params.='">';
    } else if ($vparam['type']=='textarea') { 
      $email_params.='<textarea style="height:200px; width:100%" id="'.$vparam['id'].'" type="text" class="form-control form-control-sm" data-jqs="'.base64_encode($jquery_selector).'">';
      if (isset($vparam['value'])) $email_params.=$vparam['value'];
      $email_params.='</textarea>';
    }
    $email_params.='</td>'. 
    '<td>'.(isset($vparam['px']) ? $vparam['px'] : '').'</td>'.
    '</tr>';            
  } 
          
  $email_params.='</tbody></table>';



//to json tha einai etsi
/*
$ddd=[];
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/NIPT TRF Request Consent Form_ ENG FINAL.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/Prequel_NIPT Request Consent Form_ for laboratory ENG FINAL.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/prequel_report_eng_SHRO.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/Prequel Flyer_2025.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>false,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/Prequel_Payment instructions.pdf','name_for_email'=>'neo onoma');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'base/users/31413/nikon_d7000.jpg','name_for_email'=>'');
echo '<pre>';print(json_encode($ddd));die();
*/
  
  $email_attachments='';
  $tmp=trim_gks($row['attachments']);
  //echo '<pre>'.$tmp;die();
  if ($tmp!='') {
    $tmp=json_decode($tmp,true);
    if (is_array($tmp)) {
      foreach ($tmp as $value) {
        $full_path='';
        if ($value['basefolder']=='erplo') $full_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site';
        else if ($value['basefolder']=='erpfi') $full_path=substr(GKS_FileServerShare,0,strlen(GKS_FileServerShare)-1);
        else if ($value['basefolder']=='erpul') $full_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads';
        else if ($value['basefolder']=='erpdl') $full_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install';
        else if ($value['basefolder']=='wodpr') $full_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/uploads';
        
        if ($full_path!='') {

          $full_path=$full_path.'/'.$value['relative_path'];
          if (file_exists($full_path)) {
            $atta_name=basename($value['relative_path']); 
            //if ($value['name_for_email']!='') $atta_name=$value['name_for_email'];

            $url='';
            $img_thump='';
            $url_thump='';
                        
            if ($value['basefolder']=='erplo') {
              $url=GKS_SITE_URL.'my/_current/_img_site/'.$value['relative_path'];
            } else if ($value['basefolder']=='erpfi') {
              $url='admin-get-file.php?fs=fileservers&file='.rawurlencode($value['relative_path']);
            } else if ($value['basefolder']=='erpul') {
              $url=GKS_SITE_URL.'my/uploads/'.$value['relative_path'];
            } else if ($value['basefolder']=='erpdl') {
              $url=GKS_SITE_URL.'my/install/'.$value['relative_path'];
            } else if ($value['basefolder']=='wodpr') {
              $url=GKS_SITE_URL.'wp-content/uploads/'.$value['relative_path'];
            }
            
            $myfilesize=number_format((filesize($full_path)/1024/1024),2,',','.').' MB';

            $fileext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
            if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
              
              if ($value['basefolder']=='erplo') {
                $url_thump=$url;
              } else if ($value['basefolder']=='erpfi') {
                $url_thump=dirname($value['relative_path']).'/thumbnail/' . basename($value['relative_path']);
                $url_thump='admin-get-file.php?fs=fileservers&file='.rawurlencode($url_thump);
              } else if ($value['basefolder']=='erpul') {
                $url_thump=$url;
              } else if ($value['basefolder']=='erpdl') {
                $url_thump=$url;
              } else if ($value['basefolder']=='wodpr') {
                $url_thump=$url;
              }

              if ($url_thump!='') {
                $img_thump='<img style="max-width:96px;max-height:96px;" src="'.$url_thump.'">';
              }
            }
            
            if ($url!='') {
              $url= '<a href="'.$url.'" target="_blank">'.$atta_name.'</a>'; 
            } else if ($atta_name!='') {
              $url=$atta_name;
            }
            $email_attachments.=
            '<tr class="gks_email_attachments_item">'.
              '<td style="width:50px;text-align: center;vertical-align: middle;">'.
                '<input '.($value['def_check'] ? 'checked' : '').' data-basefolder="'.$value['basefolder'].'" type="checkbox" class="dialog_item_message_email_attachments_checkbox" data-path="'.$value['relative_path'].'">'.
              '</td>'.
              '<td class="mytdcml fol_td_name">'.$url.'</td>'.
              '<td class="mytdcm tdimg">'.$img_thump.'</td>'.
              '<td class="mytdcmr" nowrap>'.$myfilesize.'</td>'.
              '<td class="mytdcm" nowrap></td>'.
            '</tr>';
          }
        }
      }
    }
  }
  
      
  
  
  $return = array(
    'success' => true, 
    'message' => base64_encode('OK'),
    'email_params'=>base64_encode($email_params),
    'email_subject' => base64_encode($row['email_subject']),
    'email_message' => base64_encode($row['email_message']),
    'email_attachments' => base64_encode($email_attachments),
  );
  echo json_encode($return); die();  
  
}
echo '<pre>ssssss '.$id.' '.$cmd;die();
