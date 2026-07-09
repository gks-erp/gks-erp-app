<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_print_forms');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_print_forms',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$gks_custom_prepare = gks_custom_table_item_prepare('gks_print_forms',['from'=>'item']);


if ($id==-1) {
  
  $copy_id=0;
  if (isset($_GET['copy'])) $copy_id=intval($_GET['copy']);
  if ($copy_id > 0) {
    //echo time();
    //die();
    //product_photo
      
    $sql="INSERT INTO gks_print_forms (
    print_form_descr,is_disable,
    gks_lang,edit_mode,file_type,grayscale,zoom,dpi,size_name,
    width_cm,height_cm,is_landscape,margin_cm_left,margin_cm_right,margin_cm_top,margin_cm_bottom,
    logo_url,page_header,page_footer,page_background_url,page_background_opacity,
    form_header,form_footer,details_header,details_body,details_footer,fpa_analysis,foroi_analysis,lots_and_serials_analysis,eidoi_optional,custom_css,custom_javascript,file_thump_url,
    perm_company_ids,perm_acc_journal_ids,perm_acc_seires_ids,
    
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
    
    SELECT CONCAT(print_form_descr,' draft ".rand(1000,9999)."') as product_code_new,0,
    gks_lang,edit_mode,file_type,grayscale,zoom,dpi,size_name,
    width_cm,height_cm,is_landscape,margin_cm_left,margin_cm_right,margin_cm_top,margin_cm_bottom,
    logo_url,page_header,page_footer,page_background_url,page_background_opacity,
    form_header,form_footer,details_header,details_body,details_footer,fpa_analysis,foroi_analysis,lots_and_serials_analysis,eidoi_optional,custom_css,custom_javascript,file_thump_url,
    perm_company_ids,perm_acc_journal_ids,perm_acc_seires_ids,
    
    now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
    from gks_print_forms
    WHERE id_print_form=".$copy_id;
    
    //print '<pre>';
    //print $sql;
    //die();
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $id = $db_link->insert_id;

    
    
    $sql="insert into gks_print_objects_forms (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      print_form_id,print_object_id) 
    SELECT now() as mydate_add,now() as mydate_edit,".$my_wp_user_id." as user_id_add,".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    ".$id." as print_form_id, print_object_id
    FROM gks_print_objects_forms 
    WHERE print_form_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    //var_dump($id);
    //die();    
    if ($id > 0) {
      header('Location: ?id='.$id);
      die();
    }
  }
    
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_print_form']=-1;
  $row['print_form_descr']='';
  $row['gks_lang']='el-GR';
  $row['edit_mode']='html';
  $row['file_type']='pdf';
  $row['is_landscape']=0;
  $row['grayscale']=0;
  $row['zoom']=1;
  $row['logo_url']='';
  $row['page_background_url']='';
  $row['page_background_opacity']=0.5;
  $row['is_disable']=0;
  $row['size_name']='a4';
  $row['width_cm']=21;
  $row['height_cm']=29.7;
  
  $row['margin_cm_left']=2;
  $row['margin_cm_right']=2;
  $row['margin_cm_top']=2;
  $row['margin_cm_bottom']=2;
  $row['dpi']=600;
  
  $row['page_header']='';
  $row['form_header']='';
  $row['details_header']='';
  $row['details_body']='';
  $row['details_footer']='';
  $row['form_footer']='';
  $row['page_footer']='';
  $row['fpa_analysis']='';
  $row['foroi_analysis']='';
  $row['lots_and_serials_analysis']='';
  $row['eidoi_optional']='';
  $row['custom_css']='';
  $row['custom_javascript']='';

  
  
  $row['file_thump_url']='';
  $row['localization_set_id']=0;
  $row['sortorder']=1000;
  
  
  $row['perm_company_ids']='';
  $row['perm_acc_journal_ids']='';
  $row['perm_acc_seires_ids']='';
  
  $my_page_title=gks_lang('Νέα Φόρμα Εκτύπωσης');

  
  

} else {
  $sql ="SELECT gks_print_forms.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_lang.lang_name
  FROM ((gks_print_forms 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_print_forms.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_print_forms.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  where id_print_form = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Φόρμα Εκτύπωσης').': '.$row['print_form_descr'];
  $object_title=$row['print_form_descr'];
}

$edit_mode=$row['edit_mode'];
$file_type=$row['file_type'];

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


$paper_sizes=gks_paper_sizes();
$row['size_name']='';
foreach ($paper_sizes as $value) {
  if ($row['width_cm']*10 == $value['width_mm'] and $row['height_cm']*10 == $value['height_mm']) {
    $row['size_name']=$value['name'];
    break;
  }
}

$localization_set_id=$row['localization_set_id'];



$sql_fobjects="SELECT gks_print_objects.id_print_object,gks_print_objects.object_descr
FROM gks_print_objects_forms 
LEFT JOIN gks_print_objects ON gks_print_objects_forms.print_object_id = gks_print_objects.id_print_object
WHERE gks_print_objects_forms.print_form_id=".$id."
ORDER BY gks_print_objects.object_descr";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {debug_mail(false,'error sql',$sql_fobjects);die('sql error');}
$fobjects=array();
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $fobjects[]=gks_lang($row_fobjects['object_descr'],'part4','object_descr');
}
$fobjects_text=implode(']][[',$fobjects);


$gks_fobjects_tags=array();
$max_ids=array();
gks_print_form_get_maxids($gks_fobjects_tags,$max_ids);
//print '<pre>';print_r($max_ids);print_r($gks_fobjects_tags);die();
//print '<pre>';print_r($max_ids);print_r($gks_fobjects_tags);die();

$fobjectsjs='var fobjects_max_ids=[];'."\n";
foreach ($max_ids as $value) {
  $fobjectsjs.='fobjects_max_ids.push({id:'.$value['id'].',ctid:'.$value['ctid'].',name:\''.$value['name'].'\',descr:\''.base64_encode(gks_lang($value['descr'],'part4','object_descr')).'\',maxid:'.$value['maxid'].'})'.";\n";
} 



stat_record();



function this_html_add_code($html_in) {
  global $edit_mode;
  global $file_type;
  if ($edit_mode=='raw') return $html_in;
  if ($file_type=='raw') return $html_in;
  
  //$pattern='~\{((?:[^\{\}]++|(?R))*)\}~'; // { 
  //$pattern="~\[((?:[^\[\]]++|(?R))*)\]~"; // [
  //preg_match_all( $pattern, $html_in, $matches);
  
  $html_out=$html_in;
//  foreach ($matches[0] as $value) {
//    //$html_out=str_replace($value, htmlspecialchars_gks($value), $html_out);
//    $html_out=str_replace($value, htmlentities($value), $html_out);
//  } 
  //print_r($html_out);
  //die();
  
  return '<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>gks</title>
  <base href="'.GKS_SITE_URL.'">
  <link href="'.GKS_SITE_URL.'my/css/fontawesome-all.min.css" rel="stylesheet">
  <style type="text/css">

    thead { display: table-header-group; }
    tfoot { display: table-row-group; }
    tr { page-break-inside: avoid; }  
    th { page-break-inside: avoid; }  
  </style>
</head>
<body>

'.
$html_out.  
  
'

</body>
</html>';    
}



include_once('_my_header_admin.php');
?>
<style>
.gks_curr_tinymce {
  font-family:courier;  
}
#zoom_slider_handle {
  width: 50px;
  height: 30px;
  top: 50%;
  margin-top: -15px;
  text-align: center;
  line-height: 1.6em;
  padding: 5px 5px;
  margin-left: -25px;
  cursor: pointer;
  font-size: 80%;
}
.local_set_lang > option:disabled {
  background-color: lightgray;
  color: black;
  
}  
#edit_mode_div {
  display: flex;flex-direction: row;gap: 20px;justify-content: center;
}  
.edit_mode_div_disabled {
  opacity:0.5;  
}
</style>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Φόρμα Εκτύπωσης');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Φόρμα Εκτύπωσης');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 

          
          <div class="form-group row">
            <label for="print_form_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="print_form_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['print_form_descr']);?>">
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="gks_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-md-8">
              <select id="gks_lang" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $local_set_lang_current='';
                $langs_array=array();
                
                $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                ".$lang_prepare_gks_lang['sql']['from2']."
                ORDER BY lang_sortorder,lang_name";                  
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_lang'].'" ';
                  if ($row_select['id_lang']==$row['gks_lang']) {
                    echo ' selected ';
                    $local_set_lang_current=$row_select['lang_name'];
                  }
                  echo '>'.$row_select['lang_name'].'</option>';
                  $langs_array[]=array('id'=>$row_select['id_lang'], 'descr' => $row_select['lang_name']);
                }?>
              </select>    
            </div>
          </div>           
          
          <div class="form-group row">
            <label for="file_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="file_type" id="file_type_pdf" value="pdf" class="myneedsave" style="cursor: pointer;" <?php if ($file_type=='pdf') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
                <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:150%"></i>
                </label>
              <input type="radio" name="file_type" id="file_type_html"  value="html" class="myneedsave" style="cursor: pointer;" <?php if ($file_type=='html') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
                <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>
                </label>
              <input type="radio" name="file_type" id="file_type_jpg"  value="jpg" class="myneedsave" style="cursor: pointer;" <?php if ($file_type=='jpg') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
                <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:21px;vertical-align: top;">
                </label>
              <input type="radio" name="file_type" id="file_type_raw"  value="raw" class="myneedsave" style="cursor: pointer;" <?php if ($file_type=='raw') echo 'checked';?>>  
                <label class="form-control-sm" for="file_type_raw" style="display:inline;padding-right:18px;cursor: pointer;">raw
                <i class="fas fa-code tooltipster" title="raw" style="color:#4e4e4e;font-size:150%"></i>
                </label>
                
            </div>
          </div>           

          <div class="form-group row">
            <label for="is_landscape" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσανατολισμός');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="is_landscape" id="is_landscape_off" value="1" class="myneedsave" style="cursor: pointer;" <?php if ($row['is_landscape']==0) echo 'checked';?>>  
                <label class="form-control-sm" for="is_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
                <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:150%"></i>
                </label>
              <input type="radio" name="is_landscape" id="is_landscape_on"  value="2" class="myneedsave" style="cursor: pointer;" <?php if ($row['is_landscape']!=0) echo 'checked';?>>  
                <label class="form-control-sm" for="is_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
                <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:150%"></i>
                </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="grayscale" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="grayscale" id="grayscale_off" value="1" class="myneedsave" style="cursor: pointer;" <?php if ($row['grayscale']==0) echo 'checked';?>>  
                <label class="form-control-sm" for="grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
                <img src="img/palette-color.png" border="0" width="16">
                </label>
              <input type="radio" name="grayscale" id="grayscale_on"  value="2" class="myneedsave" style="cursor: pointer;" <?php if ($row['grayscale']!=0) echo 'checked';?>>  
                <label class="form-control-sm" for="grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
                <img src="img/palette-gray.png" border="0" width="16">
                </label>
            </div>
          </div>
          

          <div class="form-group row">
            <label for="zoom" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μεγέθυνση');?>:</label>
            <div class="col-md-8">
              <div id="zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
                <div id="zoom_slider_handle" class="ui-slider-handle"></div>
              </div>
            </div>
          </div>          
          

          <div class="form-group row">
            <label for="logo_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url για Λογότυπο');?>:</label>
            <div class="col-md-8">
              <input id="logo_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['logo_url']);?>" placeholder="<?php echo gks_lang('π.χ.');?> https://www.gks.gr/image1.jpg">
            </div>
          </div> 
          <div class="form-group row">
            <label for="page_background_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Url για Υδατογράφημα');?>:</label>
            <div class="col-md-8">
              <input id="page_background_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['page_background_url']);?>" placeholder="<?php echo gks_lang('π.χ.');?> https://www.gks.gr/image1.jpg">
            </div>
          </div> 
          <div class="form-group row">
            <label for="page_background_opacity" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αδιαφάνεια για Υδατογράφημα');?>:</label>
            <div class="col-md-8">
              <input id="page_background_opacity" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['page_background_opacity'];?>" placeholder="<?php echo gks_lang('π.χ.');?> 0.5" min="0" max="1" step="0.01" style="max-width:150px;">
              <small><?php echo gks_lang('Από 0,00 έως 1,00');?></small>
            </div>
          </div> 
          
          
          
          <div class="form-group row">
            <label for="is_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_disable" value="1" <?php if ($row['is_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sortorder']);?>">
            </div>
          </div>          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σε άλλες γλώσσες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('langs');?>> 
<?php
$localization_sets=array();
if ($localization_set_id>0) {
  $sql_localization="SELECT gks_print_forms.id_print_form, gks_print_forms.print_form_descr, gks_print_forms.gks_lang, gks_lang.lang_name
  FROM gks_print_forms LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.localization_set_id=".$localization_set_id."
  and gks_print_forms.id_print_form<>".$id."
  ORDER BY gks_lang.lang_name";
  $result_localization = $db_link->query($sql_localization);        
  if (!$result_localization) {debug_mail(false,'error sql',$sql_localization);die('sql error');}
  while ($row_localization = $result_localization->fetch_assoc()) {
    $localization_sets[]=$row_localization;
  }
  //print '<pre>'; print_r($localization_sets);die();
}
?>
<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Γλώσσα');?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Φόρμα Εκτύπωσης');?></th>        
    </tr>
</thead>
<tbody>
<tr class="" data-id="0">
  <th scope="row" nowrap align="right">1</td>      
  <td nowrap align="center"></td>
  <td nowrap>
    <span class="form-control-sm" id="local_set_lang_current"><?php echo $local_set_lang_current;?></span>
  </td>
  <td nowrap>
    <span class="form-control-sm"><?php echo gks_lang('Τρέχον φόρμα');?></span>
  </td>
</tr>  
<?php
$local_set_index = 1;
foreach ($localization_sets as $row_list) {
  

  $local_set_index++;
  ?>
  <tr class="local_set_tr" data-id="<?php echo $local_set_index;?>">
    <th scope="row" nowrap class="mytdcm local_set_aa"><?php echo $local_set_index;?></td>      
    <td nowrap class="mytdcm"><i class="fas fa-trash-alt local_set_index_remove" data-id="<?php echo $local_set_index;?>"></i></td>
    
    <td nowrap class="mytdcm">
      <select class="form-control form-control-sm myneedsave local_set_lang" style="width:100%;" data-id="<?php echo $local_set_index;?>">
        <option value=""></option>
        <?php
        foreach ($langs_array as $lang_rec) {
          echo '<option value="'.$lang_rec['id'].'" ';
          if ($lang_rec['id']==$row_list['gks_lang']) echo ' selected ';
          echo '>'.$lang_rec['descr'].'</option>';
        }?>
      </select>
      
      
          
    </td>
    <td nowrap class="mytdcm">
      <input type="text" class="form-control form-control-sm myneedsave local_set_form" value="<?php echo $row_list['print_form_descr'];?>"
      data-id="<?php echo $local_set_index;?>"
      style="width:calc(100% - 22px);display:inline;" 
      placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
      data-form_id="<?php echo $row_list['id_print_form'];?>">
      <a class="local_set_form_link" data-id="<?php echo $local_set_index;?>" href="admin-print_forms-item.php?id=<?php echo $row_list['id_print_form'];?>" tabindex="-1">
        <i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή Φόρμας');?>"></i>
      </a>
    </td>
  </tr>
<?php } ?>


  <tr class="" id="tr_new_button">
    <th scope="row" colspan="2" class="mytdcm"><i class="fas fa-plus-circle gks_gen_add"></i></td>      
    <td nowrap colspan="5">
      <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_local_set"><?php echo gks_lang('Προσθήκη');?></button>
    </td>  
  </tr>   
  
</tbody>
</table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μέγεθος εκτύπωσης και περιθώρια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('size');?>>      
          <div class="form-group row">
            <label for="size_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγεθος');?>:</label>
            <div class="col-md-8">
              <select id="size_name" class="form-control form-control-sm myneedsave" style="max-width: 150px;">
                <option value=""><?php echo gks_lang('Προσαρμοσμένο');?></option>
                <?php
                foreach ($paper_sizes as $row_select) {
                  echo '<option value="'.$row_select['name'].'" '.
                  'data-width="'.myNumberFormatNo0($row_select['width_mm']/10).'" '.
                  'data-height="'.myNumberFormatNo0($row_select['height_mm']/10).'" ';
                  if ($row_select['name']==$row['size_name']) echo ' selected ';
                  echo '>'.$row_select['name'].'</option>';
                } ?>
              </select>    
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="width_cm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλάτος σε cm');?>:</label>
            <div class="col-md-8">
              <input id="width_cm" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['width_cm']);?>" min="0" step="0.1" <?php if ($row['size_name']!='') echo 'disabled';?> style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="height_cm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ύψος σε cm');?>:</label>
            <div class="col-md-8">
              <input id="height_cm" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['height_cm']);?>" min="0" step="0.1" <?php if ($row['size_name']!='') echo 'disabled';?> style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="margin_cm_left" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιθώριο αριστερά σε cm');?>:</label>
            <div class="col-md-8">
              <input id="margin_cm_left" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['margin_cm_left']);?>" min="0" step="0.1" style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="margin_cm_right" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιθώριο δεξιά σε cm');?>:</label>
            <div class="col-md-8">
              <input id="margin_cm_right" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['margin_cm_right']);?>" min="0" step="0.1" style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="margin_cm_top" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιθώριο επάνω σε cm');?>:</label>
            <div class="col-md-8">
              <input id="margin_cm_top" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['margin_cm_top']);?>" min="0" step="0.1" style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="margin_cm_bottom" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιθώριο κάτω σε cm');?>:</label>
            <div class="col-md-8">
              <input id="margin_cm_bottom" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['margin_cm_bottom']);?>" min="0" step="0.1" style="max-width: 150px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="dpi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('dpi');?>:</label>
            <div class="col-md-8">
              <input id="dpi" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['dpi'];?>" min="100" step="50" max="1200" style="max-width: 150px;">
            </div>
          </div> 


        </div>
      </div>



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Περιορισμοί');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('apply');?>>      
          <div class="form-group row">
            <label for="gks_fobjects" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντικείμενα');?>:</label>
            <div class="col-md-8" id="field_gks_fobjects">
              <input id="gks_fobjects" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $fobjects_text;?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <?php
              $perm_company_ids_tags=array();
              $value='';
              $rdata_read=array();
              $temp=trim_gks($row['perm_company_ids']);
              if ($temp!='') $rdata_read=unserialize($temp);
              //print '<pre>';print_r($rdata_read);//die();
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
              if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
              $rdata=array();
              while ($rowtag = $resulttags->fetch_assoc()) {
                $mytag_id=$rowtag['id_company'];
                $mytag=$rowtag['company_title'];
                if (isset($rowtag['id_company_sub'])==false) {
                  $mytag_id.='|0';
                  $mytag.=' \ '.gks_lang('Κεντρικό');
                } else {
                  $mytag_id.='|'.$rowtag['id_company_sub'];
                  $mytag.=' \ '.$rowtag['company_sub_title'];
                }
                $temp=$mytag.' (#'.$mytag_id.')';
                $perm_company_ids_tags[]=$temp;
                if (is_array($rdata_read) and in_array($mytag_id,$rdata_read)) {
                  $rdata[]=$temp;
                }
              }
              if (count($rdata)>0) $value=implode(']][[',$rdata);
              echo '<input id="perm_company_ids" value="'.htmlspecialchars_gks($value).'" class="form-control form-control-sm myneedsave" type="text">';
              ?>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγια');?>:</label>
            <div class="col-md-8">
              <?php
              $value='';
              $rdata=trim_gks($row['perm_acc_journal_ids']);
              if ($rdata!='') {
                $rdata=unserialize($rdata);
                if (count($rdata)>0) {
                  $sqltags="select id_acc_journal as myid,acc_journal_descr as mytag from gks_acc_journal where id_acc_journal in (".implode(',',$rdata).") order by sortorder";
                  $resulttags = $db_link->query($sqltags);        
                  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                  $rdata=array();
                  while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                  if (count($rdata)>0) $value=implode(']][[',$rdata);
                }
              }
              echo '<input id="perm_acc_journal_ids" value="'.htmlspecialchars_gks($value).'" class="form-control form-control-sm myneedsave" type="text">';
              ?>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρές');?>:</label>
            <div class="col-md-8">
              <?php
              $value='';
              $rdata=trim_gks($row['perm_acc_seires_ids']);
              if ($rdata!='') {
                $rdata=unserialize($rdata);
                if (count($rdata)>0) {
                  $sqltags="select id_acc_seira as myid,seira_descr as mytag from gks_acc_seires where id_acc_seira in (".implode(',',$rdata).") order by sortorder";
                  $resulttags = $db_link->query($sqltags);        
                  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                  $rdata=array();
                  while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                  if (count($rdata)>0) $value=implode(']][[',$rdata);
                }
              }
              echo '<input id="perm_acc_seires_ids" value="'.htmlspecialchars_gks($value).'" class="form-control form-control-sm myneedsave" type="text">';

              ?>
            </div>
          </div>
             
        </div>
      </div>


<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>

    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12" style="margin-top: 24px;margin-bottom: 24px;">
      <div id="edit_mode_div" class="<?php if ($file_type=='raw') echo 'edit_mode_div_disabled';?>">
        <div><?php echo gks_lang('Επεξεργασία');?>: </div>
        <div>
          <input type="radio" name="edit_mode" id="edit_mode_html" value="html" class="myneedsave" style="cursor: pointer;" <?php if ($row['edit_mode']=='html') echo 'checked';?> <?php if ($file_type=='raw') echo 'disabled';?>>
          <label class="form-control-sm" for="edit_mode_html" style="display:inline;padding-right:18px;cursor: pointer;">html
            <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>
          </label>
        </div>
        <div>
          <input type="radio" name="edit_mode" id="edit_mode_raw" value="raw" class="myneedsave" style="cursor: pointer;" <?php if ($row['edit_mode']=='raw') echo 'checked';?> <?php if ($file_type=='raw') echo 'disabled';?>>
          <label class="form-control-sm" for="edit_mode_raw" style="display:inline;padding-right:18px;cursor: pointer;">raw
            <i class="fas fa-code tooltipster" title="raw" style="color:#4e4e4e;font-size:150%"></i>
          </label>        
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κεφαλίδα Σελίδας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('header');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="page_header" name="page_header"><?php echo this_html_add_code($row['page_header']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
        

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κεφαλίδα Φόρμας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('form1');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="form_header" name="form_header"><?php echo this_html_add_code($row['form_header']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

        
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κεφαλίδα Λεπτομερειών');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('detail1');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="details_header" name="details_header"><?php echo this_html_add_code($row['details_header']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λεπτομέρεια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('detail2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="details_body" name="details_body"><?php echo this_html_add_code($row['details_body']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  
      
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υποσέλιδο Λεπτομέρειας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('detail3');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="details_footer" name="details_footer"><?php echo this_html_add_code($row['details_footer']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>        

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υποσέλιδο Φόρμας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('form2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="form_footer" name="form_footer"><?php echo this_html_add_code($row['form_footer']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υποσέλιδο Σελίδας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('page2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="page_footer" name="page_footer"><?php echo this_html_add_code($row['page_footer']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="alert alert-primary text-center" role="alert">
        <?php echo gks_lang('Ειδικές περιπτώσεις');?>
      </div>      
    </div>
  </div>
</div> 

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ανάλυση ΦΠΑ');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('fpa');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="fpa_analysis" name="fpa_analysis"><?php echo this_html_add_code($row['fpa_analysis']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ανάλυση άλλων φόρων, τελών και κρατήσεων');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('anafor');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="foroi_analysis" name="foroi_analysis"><?php echo this_html_add_code($row['foroi_analysis']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ανάλυση Παρτίδων και Serial Numbers');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('lots');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="lots_and_serials_analysis" name="lots_and_serials_analysis"><?php echo this_html_add_code($row['lots_and_serials_analysis']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προτεινόμενα Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('optional');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="eidoi_optional" name="eidoi_optional"><?php echo this_html_add_code($row['eidoi_optional']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 
          
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand" id="custom_css_div">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('CSS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('css');?>>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <textarea id="custom_css" style="border:1px solid black;"><?php echo $row['custom_css'];?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand" id="custom_javascript_div">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('JavaScript');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('javascript');?>>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <textarea id="custom_javascript" style="border:1px solid black;"><?php echo $row['custom_javascript'];?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_preview"><?php echo gks_lang('Προεπισκόπηση');?></button>
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_print_form'];?>" data-model="gks_print_forms" data-backurl="admin-print_forms.php"><?php echo gks_lang('Διαγραφή');?></button>
      <button id="submit_button_copy" type="button" class="btn btn-primary tooltipster" onclick="window.location.href='admin-print_forms-item.php?id=-1&copy=<?php echo $id;?>'"  title="<?php echo gks_lang('Δημιουργία αντιγράφου');?>"><i class="fas fa-copy" style="font-size: 120%;"></i></button>
      <button id="submit_button_export" type="button" class="btn btn-primary tooltipster"  title="<?php echo gks_lang('Εξαγωγή');?>"><i class="fas fa-download" style="font-size: 120%;"></i></button>
      <?php } ?>
      <button id="submit_button_import" type="button" class="btn btn-primary tooltipster"  title="<?php echo gks_lang('Εισαγωγή');?>"><i class="fas fa-upload" style="font-size: 120%;"></i></button>
      <input id="submit_button_import_file" type="file" accept="application/json" style="display:none;"/>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      
      <?php 
      echo getObjectRels('gks_print_forms',$id);
      echo getActivityObjectTable('gks_print_forms',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_print_forms','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      

    </div>
    
    <div class="col-xl-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μικρογραφία');?>
        </div>
        <div class="card-body text-center" <?php echo gks_card_body('thump');?>>      
          <img id="img_file_thump_url" class="img-fluid" src="<?php 
          if (empty($row['file_thump_url'])) echo 'img/print_form_empty.png';
          else echo $row['file_thump_url'];
          
          ?>" border="0" 
          style="max-width:100%;max-height:100%;margin: auto;border: 1px solid gray;box-shadow: 10px 10px 5px #aaaaaa;"/>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_print_form']>0) echo $row['id_print_form'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
        </div>
      </div>      
    </div>
    
  </div>
</div>


<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Προεπισκόπησης');?></div>
    </div>

    <div class="row">
      <label class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αντικείμενο');?>:</label>
      <div class="col-sm-6" id="fobjects_col">
      <?php
      $fobject_sel_val=0;
      $fovc=0;
      foreach ($max_ids as $vname => $bbbb) {
        if (in_array($bbbb['descr'],$fobjects)) {
          $fovc++;
          if ($fovc==1) $fobject_sel_val=$bbbb['maxid'];
          echo '<div class="div_fobject" '.
          'data-id="'.$bbbb['id'].'" '.
          'data-ctid="'.$bbbb['ctid'].'" >'.
          '<input type="radio" name="fobject_sel" '.
          'value="'.$bbbb['id'].'" '.
          'id="id_print_object_'.$bbbb['id'].'" '.
          ($fovc==1 ? 'checked' : '').'> '.
          '<label for="id_print_object_'.$bbbb['id'].'">'.$bbbb['descr'].'</label></div>';          
          
        }
      } 
      ?>
      </div>
    </div>  
    <div class="row">
      <label for="fobject_id" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ταυτότητα αντικειμένου');?> (ID):</label>
      <div class="col-sm-6">
        <input id="fobject_id" type="text" class="form-control form-control-sm" value="<?php if ($fobject_sel_val>0) echo $fobject_sel_val;?>" style="max-width:150px;">
      </div>  
    </div>  

    <div class="row">
      <label for="createthump" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Δημιουργία μικρογραφίας');?>:</label>
      <div class="col-sm-6">
        <input type="checkbox" id="createthump" value="1" class="switchery1_sel2" <?php if ($file_type!='pdf') echo 'disabled'; ?>>
      </div>
      <div class="col-sm-12">
        <small><i><?php echo gks_lang('Αυτή η λειτουργία είναι δυνατή όταν ο τύπος αρχείου είναι pdf');?><br>
          <?php echo gks_lang('Η μικρογραφία θα χρησιμοποιηθεί όταν θα θέλουμε να επιλέξουμε την συγκεκριμένη φόρμα εκτύπωσης');?><br>
          <?php echo gks_lang('To αποτέλεσμα θα πρέπει να είναι μία σελίδα');?>.
          </i></small>        
      </div>
    </div>
      
      
 
  </div>  
</div>

<?php include_once('_dialogs.php'); ?>

<script src='/my/js/tinymce/tinymce.min.js'></script>

<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_dialog_object_rel_curr='gks_print_forms';
var from_php_activity_model='gks_print_forms';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>; 



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_print_forms','delete',$id);?>;



var from_php_edit_mode='<?php echo $edit_mode;?>';
var from_php_file_type='<?php echo $file_type;?>';


var from_php_zoom=<?php echo $row['zoom']?>;

var gks_fobjects_tags = [];
<?php 
foreach ($gks_fobjects_tags as $value) {
  echo "  gks_fobjects_tags.push('".gks_lang($value,'part4','object_descr')."');"."\n";
} 
?>  
<?php
echo $fobjectsjs;
?>  
var perm_company_ids_tags = [];
<?php 
foreach ($perm_company_ids_tags as $value) {
  echo "perm_company_ids_tags.push($.base64.decode('".base64_encode($value)."'));\n";
}
?>
var perm_acc_journal_ids_tags = [];
<?php 
$sqltags="select id_acc_journal as myid, acc_journal_descr as mytag from gks_acc_journal where acc_journal_descr<>'' order by sortorder";
$resulttags = $db_link->query($sqltags);
if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
while ($rowtag = $resulttags->fetch_assoc())   echo "perm_acc_journal_ids_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
?>
var perm_acc_seires_ids_tags = [];
<?php 
$sqltags="select id_acc_seira as myid, seira_descr as mytag from gks_acc_seires where seira_descr<>'' order by sortorder";
$resulttags = $db_link->query($sqltags);
if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
while ($rowtag = $resulttags->fetch_assoc())   echo "perm_acc_seires_ids_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
?>
    
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
});



</script>


<link rel="stylesheet" href="/my/js/codemirror-5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/hint/show-hint.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/fold/foldgutter.css" />
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/display/fullscreen.css">
<script src="/my/js/codemirror-5.65.16/lib/codemirror.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/css/css.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/javascript/javascript.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/show-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/css-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/edit/closebrackets.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/selection/active-line.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldcode.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldgutter.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/brace-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/comment-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/display/fullscreen.js"></script>



<script src="js/admin-print_forms-item.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


