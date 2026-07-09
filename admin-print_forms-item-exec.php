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
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Φόρμας Εκτύπωσης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_print_forms',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}







if ($id>0) {
  $sql="select * from gks_print_forms where id_print_form=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}



$preview=0; if (isset($_POST['preview'])) $preview=intval($_POST['preview']);
$fobject_sel=0; if (isset($_POST['fobject_sel'])) $fobject_sel=intval($_POST['fobject_sel']);
$fobject_id=0; if (isset($_POST['fobject_id'])) $fobject_id=intval($_POST['fobject_id']);
$createthump=0; if (isset($_POST['createthump'])) $createthump=intval($_POST['createthump']);

$print_form_descr=''; if (isset($_POST['print_form_descr'])) $print_form_descr=trim_gks(base64_decode($_POST['print_form_descr']));
if ($preview==1 and $print_form_descr=='') $print_form_descr='draft '.time();

$gks_lang='el-GR'; if (isset($_POST['gks_lang'])) $gks_lang=trim_gks(base64_decode($_POST['gks_lang']));
$edit_mode=''; if (isset($_POST['edit_mode'])) $edit_mode=trim_gks(base64_decode($_POST['edit_mode']));
if ($edit_mode!='html' and $edit_mode!='raw') $edit_mode='html';
$file_type=''; if (isset($_POST['file_type'])) $file_type=trim_gks(base64_decode($_POST['file_type']));


//print '<pre>';print $file_type; die();
$is_landscape=0; if (isset($_POST['is_landscape'])) $is_landscape=intval($_POST['is_landscape']);
$grayscale=0; if (isset($_POST['grayscale'])) $grayscale=intval($_POST['grayscale']);
$zoom=1; if (isset($_POST['zoom'])) $zoom=floatval($_POST['zoom'])/100;
$logo_url=''; if (isset($_POST['logo_url'])) $logo_url=trim_gks(base64_decode($_POST['logo_url']));
$page_background_url=''; if (isset($_POST['page_background_url'])) $page_background_url=trim_gks(base64_decode($_POST['page_background_url']));
$page_background_opacity=''; if (isset($_POST['page_background_opacity'])) $page_background_opacity=floatval($_POST['page_background_opacity']);
$is_disable=0; if (isset($_POST['is_disable'])) $is_disable=intval($_POST['is_disable']);
$size_name=0; if (isset($_POST['size_name'])) $size_name=trim_gks(base64_decode($_POST['size_name']));
$width_cm=0; if (isset($_POST['width_cm'])) $width_cm=floatval($_POST['width_cm']);
$height_cm=0; if (isset($_POST['height_cm'])) $height_cm=floatval($_POST['height_cm']);
$margin_cm_left=0; if (isset($_POST['margin_cm_left'])) $margin_cm_left=floatval($_POST['margin_cm_left']);
$margin_cm_right=0; if (isset($_POST['margin_cm_right'])) $margin_cm_right=floatval($_POST['margin_cm_right']);
$margin_cm_top=0; if (isset($_POST['margin_cm_top'])) $margin_cm_top=floatval($_POST['margin_cm_top']);
$margin_cm_bottom=0; if (isset($_POST['margin_cm_bottom'])) $margin_cm_bottom=floatval($_POST['margin_cm_bottom']);
$dpi=0; if (isset($_POST['dpi'])) $dpi=intval($_POST['dpi']);
$sortorder=0; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);

$fobjects=''; if (isset($_POST['fobjects'])) $fobjects=trim_gks(base64_decode($_POST['fobjects']));


//tha kano metatropi apo tin glosa tou xristi sta ellikina
$sql_fobjects="select object_descr from gks_print_objects where object_descr<>''";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {
  debug_mail(false,'error sql',$sql_fobjects);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$fobjects_db=[];$fobjects_db_lang=[];
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $fobjects_db[]=$row_fobjects['object_descr'];
  $fobjects_db_lang[]=gks_lang($row_fobjects['object_descr'],'part4','object_descr');
}
//print '<pre>';print_r($fobjects_db);print_r($fobjects_db_lang);die();

$fobjects_parts=explode(']][[',$fobjects);
$fobjects_texts=array();
if (count($fobjects_parts)>0) {
  foreach ($fobjects_parts as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      $myitem=$value;
      foreach ($fobjects_db_lang as $kk => $item_lang) {
        if ($item_lang==$value) {$myitem=$fobjects_db[$kk];break;}//vazo to elliniko
      } 
      $fobjects_texts[]="'".$db_link->escape_string($myitem)."'";
    }
  }
}
//print '<pre>';print_r($fobjects_texts);die();

$fobjects_ids=array();
if (count($fobjects_texts)>0) {
  $sql_fobjects="select id_print_object from gks_print_objects where object_descr in (".implode(',',$fobjects_texts).")";
  $result_fobjects = $db_link->query($sql_fobjects);        
  if (!$result_fobjects) {
    debug_mail(false,'error sql',$sql_fobjects);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row_fobjects = $result_fobjects->fetch_assoc()) {
    $fobjects_ids[]=$row_fobjects['id_print_object'];
  }
}
//print '<pre>'.$fobjects;print_r($fobjects_texts); print_r($fobjects_ids); die();



$perm_company_ids=''; if (isset($_POST['perm_company_ids'])) $perm_company_ids=trim_gks(base64_decode($_POST['perm_company_ids']));
$parts=explode(']][[',$perm_company_ids);
$parts_c=array();
foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=trim_gks($value);if ($value!='') $parts_c[]=$value;}}}   
//echo '<pre>';print_r($parts_c); //die();
if (count($parts_c)>0) {
  $sqltags="SELECT gks_company.id_company, gks_company.company_afm, gks_company.company_title, csubs.id_company_sub, csubs.company_sub_title
  FROM gks_company
  LEFT JOIN (
    SELECT id_company_sub, company_id, company_sub_title, company_sub_sortorder
    FROM gks_company_subs
    WHERE company_sub_disable=0
    union
    select 0 as id_company_sub,id_company as company_id,'".$db_link->escape_string(gks_lang('Κεντρικό'))."' as company_sub_title, 0 as company_sub_sortorder 
    from gks_company
    where company_disable=0
  ) as csubs ON gks_company.id_company = csubs.company_id
  where company_disable=0
  ORDER BY gks_company.company_sortorder, gks_company.company_title, csubs.company_sub_sortorder, csubs.company_sub_title;";
  $resulttags = $db_link->query($sqltags);        
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  $rdata=array();
  while ($rowtag = $resulttags->fetch_assoc()) {
    $mytag_id=$rowtag['id_company'];
    if (isset($rowtag['id_company_sub'])) $mytag_id.='|'.$rowtag['id_company_sub']; else $mytag_id.='|0';
    if (in_array($mytag_id,$parts_c)) {
      $rdata[]= $mytag_id;  
    }
    
  }
  $perm_company_ids=serialize($rdata);
}
//echo '<pre>';print $perm_company_ids; die();



$perm_acc_journal_ids=''; if (isset($_POST['perm_acc_journal_ids'])) $perm_acc_journal_ids=trim_gks(base64_decode($_POST['perm_acc_journal_ids']));
$parts=explode(']][[',$perm_acc_journal_ids);
$parts_c=array();
foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
if (count($parts_c)>0) {
  $sqltags="select id_acc_journal as myid from gks_acc_journal where id_acc_journal in (".implode(',',$parts_c).")";
  $resulttags = $db_link->query($sqltags);        
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  $rdata=array();
  while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=intval($rowtag['myid']);  
  $perm_acc_journal_ids=serialize($rdata);
}
//echo '<pre>';print $perm_acc_journal_ids; die();

$perm_acc_seires_ids=''; if (isset($_POST['perm_acc_seires_ids'])) $perm_acc_seires_ids=trim_gks(base64_decode($_POST['perm_acc_seires_ids']));
$parts=explode(']][[',$perm_acc_seires_ids);
$parts_c=array();
foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
if (count($parts_c)>0) {
  $sqltags="select id_acc_seira as myid from gks_acc_seires where id_acc_seira in (".implode(',',$parts_c).")";
  $resulttags = $db_link->query($sqltags);        
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  $rdata=array();
  while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=intval($rowtag['myid']);
  $perm_acc_seires_ids=serialize($rdata);
}
//echo '<pre>';print $perm_acc_seires_ids; die();

$loc_langs=''; if (isset($_POST['loc_langs'])) $loc_langs=trim_gks(base64_decode($_POST['loc_langs']));
$loc_langs_array=array();
if ($loc_langs!='') $loc_langs_array=json_decode($loc_langs, true);
//print '<pre>'; print_r($loc_langs_array); die();

$loc_langs_array_clean=array();
foreach ($loc_langs_array as $value) {
  if (isset($value['lang']) and isset($value['form_id'])) {
    $lang=trim_gks($value['lang']);
    $form_id=intval($value['form_id']);
    if ($lang!='' and $form_id>0 and $lang!=$gks_lang and isset($loc_langs_array_clean[$lang])==false) {
      $loc_langs_array_clean[$lang]=$form_id;
    }
  }
} 
//print '<pre>'; print_r($loc_langs_array_clean); die();


 
if ($print_form_descr=='') {debug_mail(false,'emptyl',           gks_lang('Ορίστε την Περιγραφή της φόρμας εκτύπωσης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή της φόρμας εκτύπωσης')));
  echo json_encode($return); die();}

if ($file_type!='pdf' and $file_type!='html' and $file_type!='jpg' and $file_type!='raw') $file_type='';
if ($file_type=='') {debug_mail(false,'emptyl',                  gks_lang('Ορίστε τον τύπο της φόρμας εκτύπωσης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον τύπο της φόρμας εκτύπωσης')));
  echo json_encode($return); die();}

if ($logo_url!=''){
  if (filter_var($logo_url, FILTER_VALIDATE_URL) === false) {
    debug_mail(false,'emptyl',                                     gks_lang('Το Url για Λογότυπο δεν είναι σωστό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Url για Λογότυπο δεν είναι σωστό')));
    echo json_encode($return); die();
  }
}
if ($page_background_url!=''){
  if (filter_var($page_background_url, FILTER_VALIDATE_URL) === false) {
    debug_mail(false,'emptyl',                                     gks_lang('Το Url για Υδατογράφημα δεν είναι σωστό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Url για Υδατογράφημα δεν είναι σωστό')));
    echo json_encode($return); die();
  }
}


if ($width_cm<2 or $width_cm > 400)  {debug_mail(false,'emptyl', gks_lang('Το Πλάτος σε cm θα πρέπει να είναι από 2 έως 400'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Πλάτος σε cm θα πρέπει να είναι από 2 έως 400')));
  echo json_encode($return); die();}
  
if ($height_cm<2 or $height_cm > 400)  {debug_mail(false,'emptyl',gks_lang('Το Ύψος σε cm θα πρέπει να είναι από 2 έως 400'));
  $return = array('success' => false, 'message' => base64_encode( gks_lang('Το Ύψος σε cm θα πρέπει να είναι από 2 έως 400')));
  echo json_encode($return); die();}
  
if ($margin_cm_left<0 or $margin_cm_left > $width_cm/2) {debug_mail(false,'emptyl',     gks_lang('Το Περιθώριο αριστερά σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($width_cm/2));
  $return = array('success' => false, 'message' => base64_encode(                       gks_lang('Το Περιθώριο αριστερά σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($width_cm/2)));
  echo json_encode($return); die();}
  
if ($margin_cm_right<0 or $margin_cm_right > $width_cm/2) {debug_mail(false,'emptyl',   gks_lang('Το Περιθώριο δεξιά σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($width_cm/2));
  $return = array('success' => false, 'message' => base64_encode(                       gks_lang('Το Περιθώριο δεξιά σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($width_cm/2)));
  echo json_encode($return); die();}
  
if ($margin_cm_top<0 or $margin_cm_top > $height_cm/2) {debug_mail(false,'emptyl',      gks_lang('Το Περιθώριο επάνω σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($height_cm/2));
  $return = array('success' => false, 'message' => base64_encode(                       gks_lang('Το Περιθώριο επάνω σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($height_cm/2)));
  echo json_encode($return); die();}
  
if ($margin_cm_bottom<0 or $margin_cm_bottom > $height_cm/2) {debug_mail(false,'emptyl',gks_lang('Το Περιθώριο κάτω σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($height_cm/2));
  $return = array('success' => false, 'message' => base64_encode(                       gks_lang('Το Περιθώριο κάτω σε cm θα πρέπει να είναι από 0 έως').' '.myNumberFormatNo0Local($height_cm/2)));
  echo json_encode($return); die();}
  


if ($dpi<10 or $dpi > 1200) {debug_mail(false,'emptyl',          gks_lang('Τα dpi θα πρέπει να είναι από 10 έως 1200)'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Τα dpi θα πρέπει να είναι από 10 έως 1200)')));
  echo json_encode($return); die();}


$paper_sizes=gks_paper_sizes();
$size_name='';
foreach ($paper_sizes as $value) {
  if ($width_cm*10 == $value['width_mm'] and $height_cm*10 == $value['height_mm']) {
    $size_name=$value['name'];
    break;
  }
}

$sql="select * from gks_print_forms where print_form_descr like '".$db_link->escape_string($print_form_descr)."' and id_print_form<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η φόρμα εκτύπωσης με όνομα <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$print_form_descr,$message);
  debug_mail(false,'already exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}





$page_header='';        if (isset($_POST['page_header']))       $page_header=trim_gks(base64_decode($_POST['page_header']));
$form_header='';        if (isset($_POST['form_header']))       $form_header=trim_gks(base64_decode($_POST['form_header']));
$details_header='';     if (isset($_POST['details_header']))    $details_header=trim_gks(base64_decode($_POST['details_header']));
$details_body='';       if (isset($_POST['details_body']))      $details_body=trim_gks(base64_decode($_POST['details_body']));
$details_footer='';     if (isset($_POST['details_footer']))    $details_footer=trim_gks(base64_decode($_POST['details_footer']));
$form_footer='';        if (isset($_POST['form_footer']))       $form_footer=trim_gks(base64_decode($_POST['form_footer']));
$page_footer='';        if (isset($_POST['page_footer']))       $page_footer=trim_gks(base64_decode($_POST['page_footer']));
$fpa_analysis='';       if (isset($_POST['fpa_analysis']))      $fpa_analysis=trim_gks(base64_decode($_POST['fpa_analysis']));
$foroi_analysis='';     if (isset($_POST['foroi_analysis']))    $foroi_analysis=trim_gks(base64_decode($_POST['foroi_analysis']));
$lots_and_serials_analysis=''; if (isset($_POST['lots_and_serials_analysis']))  $lots_and_serials_analysis=trim_gks(base64_decode($_POST['lots_and_serials_analysis']));
$eidoi_optional='';     if (isset($_POST['eidoi_optional']))    $eidoi_optional=trim_gks(base64_decode($_POST['eidoi_optional']));
$custom_css='';         if (isset($_POST['custom_css']))        $custom_css=trim_gks(base64_decode($_POST['custom_css']));
$custom_javascript='';  if (isset($_POST['custom_javascript'])) $custom_javascript=trim_gks(base64_decode($_POST['custom_javascript']));




if ($preview==1) {
  $custom_row_form=array(
  
    'print_form_descr' => $print_form_descr,
    'gks_lang' => $gks_lang,
    'file_type' => $file_type,
    'is_landscape' => $is_landscape,
    'grayscale' => $grayscale,
    'zoom' => $zoom,
    'logo_url' => $logo_url,
    'page_background_url' => $page_background_url,
    'page_background_opacity' => $page_background_opacity,
    'is_disable' => 0,
    'size_name' => $size_name,
    'width_cm' => $width_cm,
    'height_cm' => $height_cm,
    'margin_cm_left' => $margin_cm_left,
    'margin_cm_right' => $margin_cm_right,
    'margin_cm_top' => $margin_cm_top,
    'margin_cm_bottom' => $margin_cm_bottom,
    'dpi' => $dpi,
    
    'page_header' => $page_header,
    'form_header' => $form_header,
    'details_header' => $details_header,
    'details_body' => $details_body,
    'details_footer' => $details_footer,
    'form_footer' => $form_footer,
    'page_footer' => $page_footer,
    'fpa_analysis' => $fpa_analysis,
    'foroi_analysis' => $foroi_analysis,
    'lots_and_serials_analysis' => $lots_and_serials_analysis,
    'eidoi_optional' => $eidoi_optional,
    'custom_css' => $custom_css,
    'custom_javascript' => $custom_javascript,
  );
  //print '<pre>'; var_dump($custom_row_form);die();
  

  if ($fobject_sel<=0) {
    debug_mail(false,                                              gks_lang('Επιλέξτε κάποιο αντικείμενο το οποίο θα χρησιμοποιηθεί για την προεπισκόπηση'),'');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιο αντικείμενο το οποίο θα χρησιμοποιηθεί για την προεπισκόπηση')));
    echo json_encode($return); die();}

  //echo '<pre>ssssssss '.$fobject_sel;die();
  //echo '<pre>ssssssss ';print_r($fobjects_db_lang);die();

  //foreach ($fobjects_db_lang as $kk => $item_lang) {
  //  if ($fobject_sel==$item_lang) {$fobject_sel=$fobjects_db[$kk];break;}//vazo to elliniko
  //} 
  //echo '<pre>ssssssssssss '.$fobject_sel;die();

  $sql="select object_name from gks_print_objects where id_print_object=".$fobject_sel; //object_name<>'' and object_descr<>'' and object_descr like '".$db_link->escape_string($fobject_sel)."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {  
    $message=gks_lang('Δεν βρέθηκε το αντικείμενο εκτύπωσης <b>[1]</b>');
    $message=str_replace('[1]',$fobject_sel,$message);
    debug_mail(false,$message,'');
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}    
  $row = $result->fetch_assoc();
  $object_name=$row['object_name'];
  
  //echo '<pre>';echo $fobject_sel.'|'.$fobject_id.'|'.$object_name; die();
  //echo '<pre>'.$fobject_id;die();
  $fobject_id=0;
  if ($fobject_id<=0) {
    $gks_fobjects_tags=array();
    $max_ids=array();
    gks_print_form_get_maxids($gks_fobjects_tags,$max_ids);
    foreach ($max_ids as $value) {
      if ($value['id']==$fobject_sel) {
        $fobject_id=$value['maxid']; break;
      }
    } 
  }
  //echo '<pre>'.$fobject_id;die();
  
  $ctid=0;
  if ($fobject_sel>=10000) {
    $ctid=$fobject_sel;
    $object_name='gks_customt';
  }
  
  $save_basename='preview_print_form_'.$id.'_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $print_params=array(
    'table' => $object_name,
    'ctid' => $ctid,
    'id' => $fobject_id,
    'fileserver' => GKS_SITE_PATH.'tmp/',
    'folder'=> '',
    'filename' => $save_basename,
    'override' => array(
      'gks_lang' => '',     //  '' is default, 'el-GR', 'en-US' 
      'file_type' => '',    //  '' is default, 'pdf','html',
      'grayscale' => -1,    // '-1 is default', 0, 1
      'zoom' => -1,         // '-1 is default', 1, 1.5, 0.8
      'is_landscape' => -1, // '-1 is default', 0, 1
      'is_preview' => 1,
      'createthump' => $createthump,
    ),
  );  
    
  $ret_print = gks_print_form($object_name,$fobject_id,-1,$print_params,$custom_row_form);
  
  if ($ret_print['success']==false) {
    debug_mail(false,                                              gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης'),$ret_print['message']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']));
    echo json_encode($return); die();}
  
  $preview_url=$ret_print['url_file'];
  
  //print '<pre>'; print_r($ret_print);die();
  
  $file_thump_url='';
  if ($id > 0 and $createthump==1 and isset($ret_print['file_thump_url'])) {
    $file_thump_url=$ret_print['file_thump_url'];
    $sql="update gks_print_forms set file_thump_url='".$db_link->escape_string($ret_print['file_thump_url'])."' where id_print_form=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
  }
  
  
  $return = array('success' => true, 'message' => base64_encode('ok'),'preview_url' => $preview_url, 'file_thump_url' => $file_thump_url);
  echo json_encode($return); die();


}


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_print_forms');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_print_forms (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-print_forms-item.php?id='.$id); 
}

$localization_set_id=0;
while (true) {
  $localization_set_id=rand(1000000,1999999);
  $sql = "SELECT localization_set_id from gks_print_forms where localization_set_id=".$localization_set_id;
  $result = $db_link->query($sql);
  if ($result->num_rows == 0) {
    break;
  }
}


$sql="update gks_print_forms set 
print_form_descr='".$db_link->escape_string($print_form_descr)."',
gks_lang='".$db_link->escape_string($gks_lang)."',
edit_mode='".$db_link->escape_string($edit_mode)."',
file_type='".$db_link->escape_string($file_type)."',
is_landscape=".$is_landscape.",
grayscale=".$grayscale.",
zoom=".number_format($zoom,2,'.','').",
logo_url='".$db_link->escape_string($logo_url)."',
page_background_url='".$db_link->escape_string($page_background_url)."',
page_background_opacity=".$page_background_opacity.",
is_disable=".$is_disable.",
size_name='".$db_link->escape_string($size_name)."',
width_cm=".number_format($width_cm,1,'.','').",
height_cm=".number_format($height_cm,1,'.','').",
margin_cm_left=".number_format($margin_cm_left,1,'.','').",
margin_cm_right=".number_format($margin_cm_right,1,'.','').",
margin_cm_top=".number_format($margin_cm_top,1,'.','').",
margin_cm_bottom=".number_format($margin_cm_bottom,1,'.','').",
dpi=".$dpi.",
sortorder=".$sortorder.",

perm_company_ids='".$db_link->escape_string($perm_company_ids)."',
perm_acc_journal_ids='".$db_link->escape_string($perm_acc_journal_ids)."',
perm_acc_seires_ids='".$db_link->escape_string($perm_acc_seires_ids)."',


page_header='".$db_link->escape_string($page_header)."',
form_header='".$db_link->escape_string($form_header)."',
details_header='".$db_link->escape_string($details_header)."',
details_body='".$db_link->escape_string($details_body)."',
details_footer='".$db_link->escape_string($details_footer)."',
form_footer='".$db_link->escape_string($form_footer)."',
page_footer='".$db_link->escape_string($page_footer)."',
fpa_analysis='".$db_link->escape_string($fpa_analysis)."',
foroi_analysis='".$db_link->escape_string($foroi_analysis)."',
lots_and_serials_analysis='".$db_link->escape_string($lots_and_serials_analysis)."',
eidoi_optional='".$db_link->escape_string($eidoi_optional)."',
custom_css='".$db_link->escape_string($custom_css)."',
custom_javascript='".$db_link->escape_string($custom_javascript)."',

localization_set_id=".$localization_set_id.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_print_form = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql="delete from gks_print_objects_forms where print_form_id=".$id;
if (count($fobjects_ids)>0) $sql.=" and print_object_id not in (".implode(',',$fobjects_ids).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }


foreach ($fobjects_ids as $value) {
  $sql="select * from gks_print_objects_forms where print_form_id=".$id." and print_object_id=".$value;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_print_objects_forms (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      print_form_id,print_object_id
    ) values (
      now(), now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",".$value."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
} 


//echo '<pre>';echo $localization_set_id; die();
if ($localization_set_id>0 and count($loc_langs_array_clean)>0) {
  $sql="update gks_print_forms set localization_set_id=".$localization_set_id."
  where id_print_form in (".implode(',',$loc_langs_array_clean).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect,'preview_url' => '');
echo json_encode($return); die();







