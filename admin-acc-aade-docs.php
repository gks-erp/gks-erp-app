<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Έγγραφα ΑΑΔΕ μέσω myData');
$nav_active_array=array('accounting','accounting_aade_docs');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_aade_docs','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}

$company='';if (isset($_GET['company'])) $company=trim_gks($_GET['company']);
$operation=0; if (isset($_GET['operation'])) $operation=intval($_GET['operation']);
if ($operation!=0 and $operation!=1 and $operation!=2 and $operation!=3) $operation=0;
$mark='1';if (isset($_GET['mark'])) $mark=trim_gks($_GET['mark']);
$maxMark='';if (isset($_GET['maxMark'])) $maxMark=trim_gks($_GET['maxMark']);
$dateFrom='';if (isset($_GET['dateFrom'])) $dateFrom=trim_gks($_GET['dateFrom']);
$dateTo='';if (isset($_GET['dateTo'])) $dateTo=trim_gks($_GET['dateTo']);
$entityVatNumber='';if (isset($_GET['entityVatNumber'])) $entityVatNumber=trim_gks($_GET['entityVatNumber']);
$receiverVatNumber='';if (isset($_GET['receiverVatNumber'])) $receiverVatNumber=trim_gks($_GET['receiverVatNumber']);
$invType='';if (isset($_GET['invType'])) $invType=trim_gks($_GET['invType']);
$GroupedPerDay='true';if (isset($_GET['GroupedPerDay'])) $GroupedPerDay=trim_gks($_GET['GroupedPerDay']);


$fileget='';if (isset($_GET['fileget'])) $fileget=trim_gks($_GET['fileget']);

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<div id="gks_customtableview_class" style="display:none;"><?php echo $gks_customtableview_user_settings['class'][1];?></div>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φίλτρα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('filters');?>>   
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-sm-6">
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave" style="width: unset;max-width: 100%;">
                
                <?php
                //<option value="0|0"></option>
                
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if (count($user_companys)==1) echo ' selected ';
                  else if ($company==$row_select['id']) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
              
            </div>
          </div>
 
          <div class="form-group row">
            <label class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Λειτουργία');?>:</label>
            <div class="col-sm-6" style="height:unset;">
              <span style="white-space11: nowrap;">
                <input type="radio" name="form_operation" value="0" id="form_operation_ego"    <?php if ($operation==0) echo ' checked ';?>> 
                <label class="gks_label" for="form_operation_ego"    style="font-size: 0.875rem;display:inline;padding-right:18px"><?php echo gks_lang('Έστειλα εγώ');?></label>
              </span>
              <br>
              <span style="white-space11: nowrap;">
                <input type="radio" name="form_operation" value="1" id="form_operation_others" <?php if ($operation==1) echo ' checked ';?>> 
                <label class="gks_label" for="form_operation_others" style="font-size: 0.875rem;display:inline"><?php echo gks_lang('Έστειλαν άλλοι που με αφορούν');?></label>
              </span>
              <br>
              <span style="white-space11: nowrap;">
                <input type="radio" name="form_operation" value="2" id="form_operation_vatinfo" <?php if ($operation==2) echo ' checked ';?>> 
                <label class="gks_label" for="form_operation_vatinfo" style="font-size: 0.875rem;display:inline"><?php echo gks_lang('Εισροές – εκροές ΦΠΑ');?></label>
              </span>
              <br>
              <span style="white-space11: nowrap;">
                <input type="radio" name="form_operation" value="3" id="form_operation_e3info" <?php if ($operation==3) echo ' checked ';?>> 
                <label class="gks_label" for="form_operation_e3info" style="font-size: 0.875rem;display:inline"><?php echo gks_lang('Στοιχεία Ε3');?></label>
              </span>

            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group row" style="margin-bottom: 0px;">
              <div class="col-sm-12 col-md-6 col-lg-4">  
                <div class="form-group row forope0 forope1">
                  <label for="form_mark" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από ΜΑΡΚ');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_mark" type="number" class="form-control form-control-sm" value="<?php echo $mark;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width:200px;">
                  </div>              
                </div>              
                <div class="form-group row forope0 forope1">
                  <label for="form_maxMark" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έως ΜΑΡΚ');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_maxMark" type="number" class="form-control form-control-sm" value="<?php echo ($maxMark!='0' ? $maxMark : '');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width:200px;">
                  </div>              
                </div>

                <div class="form-group row forope2 forope3">
                  <label for="form_GroupedPerDay" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ομαδοποίηση ανά ημέρα');?>:</label>
                  <div class="col-sm-6">
                    <select id="form_GroupedPerDay" class="form-control form-control-sm myneedsave gks_select2">
                      <option value="true"  <?php if ($GroupedPerDay=='true' ) echo 'selected';?>><?php echo gks_lang('Ναι');?></option>
                      <option value="false" <?php if ($GroupedPerDay=='false') echo 'selected';?>><?php echo gks_lang('Όχι');?></option>
                    </select>
                  </div>              
                </div>                
                
                              
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="form-group row forope0 forope1 forope2 forope3">
                  <label for="form_dateFrom" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από Ημερομηνία');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_dateFrom" type="text" class="form-control form-control-sm" value="<?php echo $dateFrom;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width:200px;">
                  </div>              
                </div>              
                <div class="form-group row forope0 forope1 forope2 forope3">
                  <label for="form_dateTo" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έως Ημερομηνία');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_dateTo" type="text" class="form-control form-control-sm" value="<?php echo $dateTo;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width:200px;">
                  </div>              
                </div>                
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="form-group row forope0 forope1">
                  <label for="form_entityVatNumber" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ οντότητας');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_entityVatNumber" type="text" class="form-control form-control-sm" value="<?php echo $entityVatNumber;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width1:200px;">
                  </div>              
                </div>              
                <div class="form-group row forope0 forope1">
                  <label for="form_receiverVatNumber" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ αντισυμβαλλόμενου');?>:</label>
                  <div class="col-sm-6">
                    <input id="form_receiverVatNumber" type="text" class="form-control form-control-sm" value="<?php echo $receiverVatNumber;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0" style="max-width1:200px;">
                  </div>              
                </div>                
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group row">
            
              <div class="col-sm-12">
                <div class="form-group row forope0 forope1">
                  <label for="form_invType" class="col-sm-6 col-md-3 col-lg-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τύπος Παραστατικού');?>:</label>
                  <div class="col-sm-6 col-md-9 col-lg-6">

              <select id="form_invType" class="form-control form-control-sm myneedsave gks_select2">
                <option value=""></option>
                <?php
//                $sql="select * FROM gks_acc_eidi_parastatikon where is_selectable=1 ORDER BY sortorder ";
//                $result_select = $db_link->query($sql);        
//                if (!$result_select) {
//                  debug_mail(false,'admin-users-item.php error sql',$sql);
//                  die('sql error');
//                }
//                while ($row_select = $result_select->fetch_assoc()) {
//                  echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" ';
//                  if ($row_select['id_acc_eidos_parastatikou']==$row['acc_eidos_parastatikou_id']) echo ' selected ';
//                  echo '>'.$row_select['eidos_parastatikou_descr'].'</option>';
//                }
                
                
                $sql="SELECT gks_acc_eidi_parastatikon.*,
ug2.eidos_parastatikou_descr AS gt2,
ug3.eidos_parastatikou_descr AS gt3, 
ug4.eidos_parastatikou_descr AS gt4, 
ug5.eidos_parastatikou_descr AS gt5, 
ug6.eidos_parastatikou_descr AS gt6, 
ug7.eidos_parastatikou_descr AS gt7, 
ug8.eidos_parastatikou_descr AS gt8, 
ug9.eidos_parastatikou_descr AS gt9, 
ug10.eidos_parastatikou_descr AS gt10,


ug2.id_acc_eidos_parastatikou AS id2, 
ug3.id_acc_eidos_parastatikou AS id3, 
ug4.id_acc_eidos_parastatikou AS id4, 
ug5.id_acc_eidos_parastatikou AS id5,
ug6.id_acc_eidos_parastatikou AS id6,
ug7.id_acc_eidos_parastatikou AS id7,
ug8.id_acc_eidos_parastatikou AS id8,
ug9.id_acc_eidos_parastatikou AS id9,
ug10.id_acc_eidos_parastatikou AS id10,

CONCAT_WS('\\\\',
                 ug10.eidos_parastatikou_descr,
                 ug9.eidos_parastatikou_descr,
                 ug8.eidos_parastatikou_descr,
                 ug7.eidos_parastatikou_descr,
                 ug6.eidos_parastatikou_descr,
                 ug5.eidos_parastatikou_descr,
                 ug4.eidos_parastatikou_descr,
                 ug3.eidos_parastatikou_descr,
                 ug2.eidos_parastatikou_descr,
                 gks_acc_eidi_parastatikon.eidos_parastatikou_descr) as fullpath,
CONCAT_WS('\\\\',
                 ug10.eidos_parastatikou_descr,
                 ug9.eidos_parastatikou_descr,
                 ug8.eidos_parastatikou_descr,
                 ug7.eidos_parastatikou_descr,
                 ug6.eidos_parastatikou_descr,
                 ug5.eidos_parastatikou_descr,
                 ug4.eidos_parastatikou_descr,
                 ug3.eidos_parastatikou_descr,
                 ug2.eidos_parastatikou_descr) as dirpath
FROM ((((((((gks_acc_eidi_parastatikon

LEFT JOIN gks_acc_eidi_parastatikon AS ug2 ON gks_acc_eidi_parastatikon.parent_id = ug2.id_acc_eidos_parastatikou) 
LEFT JOIN gks_acc_eidi_parastatikon AS ug3 ON ug2.parent_id = ug3.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug4 ON ug3.parent_id = ug4.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug5 ON ug4.parent_id = ug5.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug6 ON ug5.parent_id = ug6.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug7 ON ug6.parent_id = ug7.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug8 ON ug7.parent_id = ug8.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug9 ON ug8.parent_id = ug9.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug10 ON ug9.parent_id = ug10.id_acc_eidos_parastatikou

where gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code<>'' 
ORDER BY gks_acc_eidi_parastatikon.sortorder,fullpath";
//or ug2.eidos_parastatikou_aade_code<>''

                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                $isgroup_open=false;$myarray=[];
                while ($row_select = $result_select->fetch_assoc()) {
                  $mypad=''; 
                  if (!empty($row_select['gt2'])) $mypad='&nbsp;&nbsp;&nbsp;';
                  if (!empty($row_select['gt3'])) $mypad='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  $mypad='';
                  
                  $aade_code=trim_gks($row_select['eidos_parastatikou_aade_code']);
                  if ($aade_code!='') {
                    $aade_code=$aade_code.' ';
                  }
                  if ($aade_code=='9.3') $row_select['eidos_parastatikou_descr']=gks_lang('Δελτίο Αποστολής');
                  
                  if ($row_select['is_selectable']==0) {
                    if ($isgroup_open) echo '</optgroup>'."\n";
                    $isgroup_open=true;
                    echo '<optgroup label="'.$mypad.$aade_code.$row_select['eidos_parastatikou_descr'].'">'."\n";
                  } else {
                    if (in_array($row_select['eidos_parastatikou_aade_code'],$myarray)==false) {
                      $myarray[]=$row_select['eidos_parastatikou_aade_code'];
                      echo '<option value="'.$row_select['eidos_parastatikou_aade_code'].'" ';
                      if ($row_select['eidos_parastatikou_aade_code']==$invType) echo ' selected ';
                      echo '>'.$mypad.$aade_code.$row_select['eidos_parastatikou_descr'].'</option>'."\n";
                    }
                  }
                }
                if ($isgroup_open) echo '</optgroup>';                
                ?>
              </select>   
              
                    
                  </div>              
                </div>              
               
              </div>
              
              
            </div>            
          </div>            
           
          <div class="form-group row">
            <div class="col-sm-12 text-sm-center">
              <button type="button" class="btn btn-primary" id="submit_start"><?php echo gks_lang('Αναζήτηση');?></button>
            </div>
          </div>           
        </div>
      </div>
    </div>
  </div>
</div>
        
<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποτελέσματα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('results');?> id="gks_results">   
          <div class="alert alert-warning" role="alert">
            <?php echo gks_lang('Κάντε μια αναζήτηση και τα αποτελέσματα θα εμφανιστούν εδώ');?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        
<style>
.mysort {
  cursor:pointer;
  color: #007bff; 
  text-decoration: none; 
}
.mysort:hover {
  color: #0056b3;
  text-decoration: underline;
}
.user_create {
  color: #35dc35;
  cursor: pointer;
  vertical-align11: middle;
}
.button_add {
  line-height: 1.0;
  padding: 3px 13px;
}
.vatitem1 {
  margin-right: 4px;
  padding-right: 4px;
  border-right: 1px solid gray  
}
.vatitem1:last-child {
  border-right: 0px solid gray 
}
</style>
   
<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_fileget='<?php echo $fileget;?>';

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#form_dateFrom').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:0}));  
  $('#form_dateTo').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:0}));  

  var sort_field='date'; //'mark';
  var sort_adesc='desc';

  function submit_start(fileget) {

    if (fileget=='') {
      company_id=0;
      company_sub_id=0;
      vcompany=$('#company_id_sub_id').val();
      if (vcompany === undefined || vcompany === null) vcompany='';
      parts=vcompany.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
            
      if (company_id<=0) {
        myalert('error:'+gks_lang('Επιλέξτε μια εταιρεία'));
        return;  
      }

      operation=parseInt($('input[name=form_operation]:checked').val());if (isNaN(operation)) operation=0;

      mark=parseInt($('#form_mark').val()); if (isNaN(mark)) mark=0;
      if (mark<=0 && operation<=1) {
        myalert('error:'+gks_lang('Το <b>Από ΜΑΡΚ</b> πρέπει να είναι αριθμός μεγαλύτερος του μηδέν'));
        return;       
      }
      maxMark=parseInt($('#form_maxMark').val());if (isNaN(maxMark)) maxMark=0;
      if (maxMark<=mark && maxMark>0 && operation<=1) {
        myalert('error:'+gks_lang('Το <b>Έως ΜΑΡΚ</b> πρέπει να είναι μεγαλύτερο από το <b>Από ΜΑΡΚ</b> ή κενό'));
        return;       
      }
      dateFrom=$('#form_dateFrom').val().trim(); if (dateFrom=='__/__/____') dateFrom='';
      dateTo=$('#form_dateTo').val().trim(); if (dateTo=='__/__/____') dateTo='';
      
      if (operation<=1) {
        if ((dateFrom!='' || dateTo!='') && (dateFrom=='' || dateTo==''))  {
          myalert('error:'+gks_lang('Θα πρέπει να ορίσετε ή και τις δύο ημερομηνίες ή καμία.'));
          return;
        }
      } else if (operation==2 || operation==3) {
        if (dateFrom=='' || dateTo=='')  {
          myalert('error:'+gks_lang('Θα πρέπει να ορίσετε και τις δύο ημερομηνίες'));
          return;
        }
      }

      entityVatNumber=$('#form_entityVatNumber').val().trim();
      receiverVatNumber=$('#form_receiverVatNumber').val().trim();
      invType=$('#form_invType').val().trim();
      
      GroupedPerDay=$('#form_GroupedPerDay').val();
      
      

            
      //console.log(company_id,company_sub_id,operation,mark,maxMark,dateFrom,dateTo,entityVatNumber,receiverVatNumber,invType,GroupedPerDay);
      //return;
      
      datasend='company_id=' + company_id;
      datasend+='&company_sub_id=' + company_sub_id;
      datasend+='&operation=' + operation;
      datasend+='&mark=' + mark;
      datasend+='&maxMark=' + maxMark;
      datasend+='&dateFrom=' + dateFrom;
      datasend+='&dateTo=' + dateTo;
      datasend+='&entityVatNumber=' + entityVatNumber;
      datasend+='&receiverVatNumber=' + receiverVatNumber;
      datasend+='&invType=' + invType;
      datasend+='&GroupedPerDay=' + GroupedPerDay;
  
      $('#gks_results').html('<div class="alert alert-info" role="alert">' +
        gks_lang('Παρακαλώ περιμένετε. Γίνεται αναζήτηση')+' ...' + 
      '</div>');
      
    } else {
      vcompany=0;
      operation=0;
      mark='';
      maxMark='';
      dateFrom='';
      dateTo='';
      entityVatNumber='';
      receiverVatNumber='';
      invType='';
      GroupedPerDay='';
      
      datasend='fileget=' + fileget;
    }
    datasend+='&sort_field=' + sort_field;
    datasend+='&sort_adesc=' + sort_adesc;
    datasend+='&gks_customtableview_class=' + $('#gks_customtableview_class').text();
    //console.log(datasend);
    
    
    
    if (fileget=='') $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-acc-aade-docs-exec.php?',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_vcompany:vcompany,
			gks_operation:operation,
			gks_mark: mark,
      gks_maxMark: maxMark,
      gks_dateFrom: dateFrom,
      gks_dateTo: dateTo,
      gks_entityVatNumber: entityVatNumber,
      gks_receiverVatNumber: receiverVatNumber,
      gks_invType: invType,
      gks_GroupedPerDay: GroupedPerDay,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
          $('#gks_results').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');				  
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					$('#gks_results').html($.base64.decode(data.html));
  					$('.mysort').click(mysort_click);
  					$('.user_create').click(user_create_click);
  					$('.button_add').click(button_add_click);
  					$('#gks_results .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});

  					// 400001830667206
  					if (data.fileget === undefined || data.fileget === null || data.fileget=='') {
  					  
  					  //window.location.hash='#' + sort_field + ',' + sort_adesc;
  					} else {
  					  from_php_fileget=data.fileget;
  					  newurl='admin-acc-aade-docs.php?company=' + this.gks_vcompany + 
  					  '&operation=' + this.gks_operation + 
  					  '&mark=' + this.gks_mark + 
  					  '&maxMark=' + this.gks_maxMark + 
  					  '&dateFrom=' + this.gks_dateFrom + 
  					  '&dateTo=' + this.gks_dateTo + 
  					  '&entityVatNumber=' + this.gks_entityVatNumber + 
  					  '&receiverVatNumber=' + this.gks_receiverVatNumber + 
  					  '&invType=' + this.gks_invType + 
  					  '&GroupedPerDay=' + this.gks_GroupedPerDay + 
  					  '&fileget=' + data.fileget;
  					  newurl+='#' + sort_field + ',' + sort_adesc;
  					  window.history.pushState({}, window.document.title, newurl);
  				  }
					} else {
					  if (data.html === undefined) 
					    myhtml= $.base64.decode(data.message)
					  else
					    myhtml= $.base64.decode(data.html);  
            $('#gks_results').html('<div class="alert alert-danger" role="alert">' +
              myhtml + 
            '</div>');
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
		      
  }
  
  $('#submit_start').click(function() {
    submit_start('');
  });
  myhash=window.location.hash;
  if (myhash.length>=4 && myhash.startsWith('#')) {
    myhash=myhash.substring(1);
    parts=myhash.split(',');
    if (parts.length==2 && parts[0].length>=2 && parts[1].length>=2) {
      sort_field=parts[0];
      sort_adesc=parts[1];
      //console.log(sort_field,sort_adesc);
    }
  }
  
  if (from_php_fileget!='') submit_start(from_php_fileget);
  
  function mysort_click() {
    sort_field=$(this).attr('data-field');
    if ($(this).parent().find('img').attr('src')=='img/desc.png') {
      sort_adesc='asc';
    } else {
      sort_adesc='desc';
    }
    window.location.hash='#' + sort_field + ',' + sort_adesc;
    //console.log(sort_field,sort_adesc);
    submit_start(from_php_fileget);
  }
  
  function user_create_click() {
    afm=$(this).attr('data-val').trim();
    if (afm=='') return;
    //console.log(afm);
    cus_sup=$(this).attr('data-cus_sup').trim();
    if (cus_sup=='') return;
    //console.log(cus_sup);

    
    url='admin-users-item.php?id=-1#createfromafm=' + afm + '|' + cus_sup;
    var win = window.open(url, '_blank');
    win.focus();
  
  }
  
  function button_add_click() {
    mark=$(this).attr('data-mark').trim();
    if (mark=='') return;
    //console.log(mark);
    
    datasend='mark=' + mark;
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-acc-aade-docs-add.php?',
			type: 'POST',
			cache: false,
			dataType: 'json',
			gks_mark:mark,
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					$('.button_add[data-mark=' + this.gks_mark + ']').parent().html($.base64.decode(data.html_out));
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});      
  }
  
  
  $('input[name="form_operation"]').change(hide_show_filter);
  function hide_show_filter() {
    operation=parseInt($('input[name=form_operation]:checked').val());if (isNaN(operation)) operation=0;
    
    $('.forope0, .forope1, .forope2, .forope3').hide();
    $('.forope' + operation).show(); 
          
  }
  hide_show_filter();
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
//db_close();
include_once('_my_footer_admin.php');

