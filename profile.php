<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

if ($my_wp_user_id <= 0) {
  header('Location: /wp-login.php?redirect_to='.urlencode(GKS_SITE_URL.'/my/profile.php'));
  die();
}


$my_page_title=gks_lang('Το προφίλ μου');
$nav_active_array=array('user','profile');

//ok
//$iban = "GR1701404870487002330000473";
//$iban = "GR5301404790479002101069178";
//$iban = "GR1601101250000000012300695";

$mynowtime =  date('Y-m-d H:00:00');

//echo $my_is_global_admin;

db_open();
stat_record();



$mytimenow=time();


//$ypoloipo_prokatavolis = get_user_ypoloipo_prokatavolis($my_wp_user_id);
//$calc = calc_profilepososto($my_wp_user_id, false);



$sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id, gks_users.ma_nomos_id, 
gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
gks_users.cv_sxesi_me_photografia, 
gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,
gks_users.profilepososto_user, gks_users.profilepososto_job,
gks_country.country_name, gks_nomoi.nomos_descr, 
table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia
FROM (((((((((".GKS_WP_TABLE_PREFIX."users 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
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
)  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mywp_capabilities
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='".GKS_WP_TABLE_PREFIX."capabilities'))
)  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
where ".GKS_WP_TABLE_PREFIX."users.id = ".$my_wp_user_id;
	
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

$user_email = trim_gks($row['user_email'].'');
$user_mobile = trim_gks($row['mymoobile'].'');
$user_login= $row['user_login'];
$user_pass_pure = $row['user_pass_pure'];
$genisi_date=$row['genisi_date'];
$mywp_capabilities = $row['mywp_capabilities'];
$profilepososto_user = $row['profilepososto_user'];
$profilepososto_job = $row['profilepososto_job'];
$old_code = $row['old_code'].'';

if (!isset($row['ma_country_id'])) $row['ma_country_id']=0;
if (!isset($row['ma_nomos_id'])) $row['ma_nomos_id']=0;
$row['user_nicename'] = get_user_meta($my_wp_user_id, 'nickname', true);



$cansendpassword=false;
if (wp_check_password($row['user_pass_pure'], $row['user_pass'], $my_wp_user_id)) {
  $cansendpassword=true;
}

$show_job_fields=false;
if (isset($_GET['cv']) and $_GET['cv']=='1') $show_job_fields = true;
if (strpos($mywp_capabilities, '"administrator"') !== false)  $show_job_fields = true;
if (strpos($mywp_capabilities, '"adminmy"') !== false)        $show_job_fields = true;
if (strpos($mywp_capabilities, '"editor"') !== false)         $show_job_fields = true;
if (strpos($mywp_capabilities, '"contributor"') !== false)    $show_job_fields = true;
if (strpos($mywp_capabilities, '"author"') !== false)         $show_job_fields = true;
if (strpos($mywp_capabilities, '"photographer"') !== false)   $show_job_fields = true;

if (strpos($mywp_capabilities, '"logistis"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"driver"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"omadarxis"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"texnikos"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"ipethinosperioxis"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"xiristismixanimaton"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"findphotos"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"hrmanager"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"babys"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"promitheutis"') !== false)   $show_job_fields = true;
if (strpos($mywp_capabilities, '"apothikarios"') !== false)   $show_job_fields = true;


//hr user jobs
if ($show_job_fields == false) {
$sql = "SELECT gks_hr_user.hr_job_id
FROM gks_hr_user
WHERE candidate_id=".$my_wp_user_id;
//AND hr_date_aitisi<='".$mynowtime."' 
//AND hr_date_canceled_by_user Is Null
//AND hr_date_reject_by_admin Is Null
//AND (hr_date_liji Is Null Or hr_date_liji>='".$mynowtime."')";

$result_hr_user = $db_link->query($sql);        
if (!$result_hr_user) {
    debug_mail(false,'profile.php error sql',$sql);
    die('sql error');
  }
  if ($result_hr_user->num_rows>=1) $show_job_fields = true;
}
if ($show_job_fields == false) {
  $sql="select candidate_id from gks_hr_interview where candidate_id=".$my_wp_user_id;
  $result_hr_user = $db_link->query($sql);        
  if (!$result_hr_user) {
    debug_mail(false,'profile.php error sql',$sql);
  die('sql error');
}
if ($result_hr_user->num_rows>=1) $show_job_fields = true;
}
$show_job_fields_style="";
if ($show_job_fields==false) $show_job_fields_style=" ;display:none ";





//part4
$ea_j='  var extra_address = [];'."\n\r"; //extra_address_javascript
$ea_a=array(); //extra_address_array
$sql="SELECT gks_users_extra_address.*, gks_country.country_name, gks_nomoi.nomos_descr
FROM (gks_users_extra_address 
LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
WHERE (((gks_users_extra_address.user_id)=".$my_wp_user_id."))
ORDER BY gks_users_extra_address.id_users_extra_address";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
}
$first_ea_j=0;
while ($row_ea = $result->fetch_assoc()) {
  if ($first_ea_j == 0) {
    $first_ea_j = $row_ea['id_users_extra_address'];
  }

  $row_ea['country_name']=gks_lang_data_trans($row_ea['country_name'],$row_ea['ea_country_id'],'gks_country','country_name');
  $row_ea['nomos_descr']=gks_lang_data_trans($row_ea['nomos_descr'],$row_ea['ea_nomos_id'],'gks_nomoi','nomos_descr');

  
  $address_name=$row_ea['ea_name'].', '.$row_ea['ea_odos'].' '.$row_ea['ea_arithmos'].', '. $row_ea['ea_orofos'].', '. $row_ea['ea_perioxi'].', '.$row_ea['ea_poli'].', '.$row_ea['ea_tk'].', '.$row_ea['country_name'].', '.$row_ea['nomos_descr'];
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);

  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);


  $item = array('id' => $row_ea['id_users_extra_address'],
                'name' => $address_name,
                'ea_name' => isset($row_ea['ea_name']) ? $row_ea['ea_name']: '' ,
                'ea_phone' => isset($row_ea['ea_phone']) ? $row_ea['ea_phone']: '' ,
                'ea_odos' => isset($row_ea['ea_odos']) ? $row_ea['ea_odos']: '' ,
                'ea_arithmos' => isset($row_ea['ea_arithmos']) ? $row_ea['ea_arithmos']: '' ,
                'ea_orofos' => isset($row_ea['ea_orofos']) ? $row_ea['ea_orofos']: '' ,
                'ea_perioxi' => isset($row_ea['ea_perioxi']) ? $row_ea['ea_perioxi']: '' ,
                'ea_poli' => isset($row_ea['ea_poli']) ? $row_ea['ea_poli']: '' ,
                'ea_tk' => isset($row_ea['ea_tk']) ? $row_ea['ea_tk']: '' ,
                'ea_country_id' => isset($row_ea['ea_country_id']) ? $row_ea['ea_country_id']: 0 ,
                'country_name' => isset($row_ea['country_name']) ? $row_ea['country_name']: '' ,
                'ea_nomos_id' => isset($row_ea['ea_nomos_id']) ? $row_ea['ea_nomos_id']: 0 ,
                'nomos_descr' => isset($row_ea['nomos_descr']) ? $row_ea['nomos_descr']: '' ,
                );
  $ea_a[$item['id']] = $item;
  
  $ea_j.="  item = {id: ".$item['id'].",";
  $ea_j.=         "name: $.base64.decode('".base64_encode($address_name)."'),";
  $ea_j.=         "ea_name: $.base64.decode('".base64_encode($item['ea_name'])."'),";
  $ea_j.=         "ea_phone: $.base64.decode('".base64_encode($item['ea_phone'])."'),";
  $ea_j.=         "ea_odos: $.base64.decode('".base64_encode($item['ea_odos'])."'),";
  $ea_j.=         "ea_arithmos: $.base64.decode('".base64_encode($item['ea_arithmos'])."'),";
  $ea_j.=         "ea_orofos: $.base64.decode('".base64_encode($item['ea_orofos'])."'),";
  $ea_j.=         "ea_perioxi: $.base64.decode('".base64_encode($item['ea_perioxi'])."'),";
  $ea_j.=         "ea_poli: $.base64.decode('".base64_encode($item['ea_poli'])."'),";
  $ea_j.=         "ea_tk: $.base64.decode('".base64_encode($item['ea_tk'])."'),";
  $ea_j.=         "ea_country_id: ".$item['ea_country_id'].",";
  $ea_j.=         "ea_nomos_id: ".$item['ea_nomos_id'].",";
  $ea_j.=         "};"."\n\r";

  $ea_j.="  extra_address[".$item['id']."]=item;"."\n\r";
  
}

if (1==2) {
  print '<pre>';
  print_r($ea_a);
  print $ea_j.'<br>';
}

include_once('_my_header_admin.php');

//die();
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>



<div id="mypostform" class="container-fluid">
<div role="main" class="container">
  <div class="row">
    <div class="col">

      <div class="accordion" id="accordion_basic_data">
        <div class="card">
          <div class="card-header" id="a_basic_data" style="background-color:#d9eadf">
            <h5 class="mb-0">
              <button class="btn btn-link gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_basic_data" aria-expanded="true" aria-controls="ac_basic_data">
                <?php echo gks_lang('Βασικά στοιχεία');?>
              </button>
            </h5>
          </div>
          <div id="ac_basic_data" class="collapse <!--show-->" aria-labelledby="a_basic_data" data-parent="#accordion_basic_data"  style="background-color:#d9eadf">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="form_user_login"><?php echo gks_lang('Όνομα χρήστη');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_user_login" id="form_user_login" value="<?php echo htmlspecialchars_gks($row['user_login']);?>" disabled>
                  <small class="form-text text-muted"><?php echo gks_lang('Το όνομα χρήστη δεν αλλάζει');?></small>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="form_first_name"><?php echo gks_lang('Το Όνομά μου');?> (<span style="color:#ff0000">*</span>) (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_first_name" id="form_first_name" value="<?php echo htmlspecialchars_gks($row['myfirst_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6">
                  <label for="form_last_name"><?php echo gks_lang('Το Επώνυμό μου');?> (<span style="color:#ff0000">*</span>) (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_last_name" id="form_last_name" value="<?php echo htmlspecialchars_gks($row['mylast_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="form_display_name"><?php echo gks_lang('Προβολή δημοσίως ως');?> (<span style="color:#ff0000">*</span>) (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_display_name" id="form_display_name" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <?php 
                    echo '<option '.($row['display_name'] == $row['user_login'] ? ' selected ' : '').'>'.$row['user_login'].'</option>';
                    if ($row['user_nicename'] !='') {echo '<option '.($row['display_name'] == $row['user_nicename'] ? ' selected ': '').'>'.$row['user_nicename'].'</option>';}
                    if ($row['myfirst_name']  !='') {echo '<option '.($row['display_name'] == $row['myfirst_name'] ? ' selected ': '').'>'.$row['myfirst_name'].'</option>';}
                    if ($row['mylast_name']   !='') {echo '<option '.($row['display_name'] == $row['mylast_name'] ? ' selected ': '').'>'.$row['mylast_name'].'</option>';}
                    if ($row['myfirst_name']  !='') {echo '<option '.($row['display_name'] == $row['myfirst_name'].' '.$row['mylast_name'] ? ' selected ': '').'>'.$row['myfirst_name'].' '.$row['mylast_name'].'</option>';}
                    if ($row['mylast_name']   !='') {echo '<option '.($row['display_name'] == $row['mylast_name'].' '.$row['myfirst_name'] ? ' selected ': '').'>'.$row['mylast_name'].' '.$row['myfirst_name'].'</option>';}
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_onoma_patera"><?php echo gks_lang('Πατρώνυμο');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_onoma_patera" id="form_onoma_patera" value="<?php echo htmlspecialchars_gks($row['onoma_patera']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_onoma_miteras"><?php echo gks_lang('Μητρώνυμο');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_onoma_miteras" id="form_onoma_miteras" value="<?php echo htmlspecialchars_gks($row['onoma_miteras']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
                          
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_gks_sex"><?php echo gks_lang('Φύλο');?> (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_gks_sex" id="form_gks_sex" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <option value="0"></option>
                    <option value="1" <?php if ($row['gks_sex']==1) echo " selected ";?>><?php echo gks_lang('Άρρεν');?></option>
                    <option value="2" <?php if ($row['gks_sex']==2) echo " selected ";?>><?php echo gks_lang('Θύλη');?></option>
                  </select>
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_display_name"><?php echo gks_lang('Γλώσσα');?> (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_gks_lang" id="form_gks_lang" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <option value=""></option>
                    <?php
                    $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                    $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                    FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                    ".$lang_prepare_gks_lang['sql']['from2']."
                    ORDER BY lang_sortorder,lang_name";                    $result_select = $db_link->query($sql);        
                    if (!$result_select) {
                      debug_mail(false,'error sql',$sql);
                      die('sql error');
                    }
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_lang'].'" ';
                      if ($row_select['id_lang']==$row['gks_lang']) echo ' selected ';
                      echo '>'.$row_select['lang_name'].'</option>';
                    }?>
                  </select>                    
                  
                  

                </div>
              </div>  
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="form_oikogeniaki_katastasti_id"><?php echo gks_lang('Οικογενειακή Κατάσταση');?> (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_oikogeniaki_katastasti_id" id="form_oikogeniaki_katastasti_id" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <option value="0"></option>
                    <?php 
                    $sql = "select * FROM gks_users_oikogeniaki_katastasti ORDER BY oikogeniaki_katastasti_sortorder;";
                    $result_select = $db_link->query($sql);
                    if (!$result_select) {debug_mail(false,'error sql',$sql);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));}
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_oikogeniaki_katastasti'].'" ';
                      if ($row_select['id_oikogeniaki_katastasti']==$row['oikogeniaki_katastasti_id']) echo ' selected ';
                      echo '>'.$row_select['oikogeniaki_katastasti_descr'].'</option>';
                    }
                    ?>                 
                  </select>                                   
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_oikogeniaki_katastasti_paidia"><?php echo gks_lang('Παιδιά');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="number" class="form-control form-control-sm1 myneedsave" name="form_oikogeniaki_katastasti_paidia" id="form_oikogeniaki_katastasti_paidia" value="<?php echo htmlspecialchars_gks($row['oikogeniaki_katastasti_paidia']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>

               
              </div>
            </div>
          </div>
        </div>
      </div>  
      
      <div class="accordion" id="accordion_password">
        <div class="card">
          <div class="card-header" id="a_password" style="background-color:#fce0de">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_password" aria-expanded="false" aria-controls="ac_password">
                <?php echo gks_lang('Κωδικός πρόσβασης');?>
              </button>
            </h5>
          </div>
          <div id="ac_password" class="collapse" aria-labelledby="a_password" data-parent="#accordion_password" style="background-color:#fce0de">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_password_old"><?php echo gks_lang('Παλιός κωδικός');?></label>
                  <input type="password" class="form-control form-control-sm1 myneedsave" name="form_password_old" id="form_password_old" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_password_new1"><?php echo gks_lang('Νέος Κωδικός');?></label>
                  <input type="password" class="form-control form-control-sm1 myneedsave" name="form_password_new1" id="form_password_new1" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_password_new2"><?php echo gks_lang('Νέος Κωδικός ξανά');?></label>
                  <input type="password" class="form-control form-control-sm1 myneedsave" name="form_password_new2" id="form_password_new2" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>                
            </div>
          </div>
        </div>
      </div>  
      
      <div class="accordion" id="accordion_photos">
        <div class="card">
          <div class="card-header" id="a_photos" style="background-color:#9f96b1">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_photos" aria-expanded="false" aria-controls="ac_photos">
                <?php echo gks_lang('Φωτογραφίες');?>
              </button>
            </h5>
          </div>
          <div id="ac_photos" class="collapse" aria-labelledby="a_photos" data-parent="#accordion_photos" style="background-color:#9f96b1">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-8" style="">
                  <p><?php echo gks_lang('Οι φωτογραφίες μου');?></p>
                  <form role="form" method="post" action="profile-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data">
                    <div id="lightgallery_user">
                      <div class="form-group" id="imagelist_photo">
                      <?php   
                        $sql="select * from gks_users_photo where user_id=".$my_wp_user_id." and filesobjectlist=0 order by id_user_photo";
                        $result_select = $db_link->query($sql);        
                        if (!$result_select) {
                          debug_mail(false,'error sql',$sql);
                          die('sql error');
                        }
                        while ($row_select = $result_select->fetch_assoc()) {
                          $photo_url = $row_select['photo_url'];
                          $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);
  
                          //echo '<span id="item_upload_photo_' . $row_select['id_user_photo'] . '"><a href="'.$photo_url_thumb.'" target="_blank">'.mb_basename($photo_url_thumb).' ('.number_format($row_select['mysize']/1024/1024,2,',','.').' MB) </a> <a href="" class="delete_upload_photo" data-id="'.$row_select['id_user_photo'].'"><img src="/my/img/0.png" border="0" width="10"></a><br></span>';
                          
                          ?>
                          <div id="item_upload_photo_<?php echo $row_select['id_user_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                            <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                              <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                            </a>
                            <br>
                            <div style="padding-top:4px">
                              <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως φωτογραφία προφίλ');?>"><img src="/my/img/profile.png" border="0" width="16"></a>
                              <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_user_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                            </div>
                          </div>
                        <?php }?>
                      </div>
                    </div>
                    <?php gks_f_button_add_files_photo_html('gks_users',0);?>
                  </form>                     
                </div>
                <div class="form-group col-md-4" style="">
                  <p><?php echo gks_lang('H φωτογραφία του προφίλ μου');?> (<span style="color:#00aa00">*</span>)</p>
                  <p style="padding-top: 8px;">
                    <?php
                    $user_photo_value="";
                    $myimgurl = get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
                    //echo $myimgurl;
                    if ($myimgurl.'' == '') {
                      $myimgurl="/my/img/avatar.png";
                    } else {
                      $user_photo_value = $myimgurl;
                    }
                    ?>
                    <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_user_photo_img"/><br>
                    
                    <a href="" id="reset_profile_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                      if ($user_photo_value == '') {
                        echo ' style="display:none" ';
                      }
                      ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
                    <br><input type="hidden" id="form_user_photo" name="form_user_photo" value="<?php echo $user_photo_value;?>" />
                  </p>                    
                </div>
              </div> 
            </div>
          </div>
        </div>
      </div>

      <div class="accordion" id="accordion_contact">
        <div class="card">
          <div class="card-header" id="a_contact" style="background-color:#ffedd1">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_contact" aria-expanded="false" aria-controls="ac_contact">
                <?php echo gks_lang('Στοιχεία επικοινωνίας');?>
              </button>
            </h5>
          </div>
          <div id="ac_contact" class="collapse" aria-labelledby="a_contact" data-parent="#accordion_contact" style="background-color:#ffedd1">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_user_email"><?php echo gks_lang('Email');?> (<span style="color:#ff0000">*</span>) (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_user_email" id="form_user_email" value="<?php echo htmlspecialchars_gks($row['user_email']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_user_mobile"><?php echo gks_lang('Κινητό Τηλέφωνο');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_user_mobile" id="form_user_mobile" value="<?php echo htmlspecialchars_gks($row['mymoobile']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_phone_home"><?php echo gks_lang('Σταθερό Τηλέφωνο');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_phone_home" id="form_phone_home" value="<?php echo htmlspecialchars_gks($row['phone_home']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>                
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_user_url"><?php echo gks_lang('Ιστότοπος');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_user_url" id="form_user_url" value="<?php echo htmlspecialchars_gks($row['user_url']); ?>" placeholder="<?php echo gks_lang('π.χ.');?> https://www.mysite.gr" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_user_url"><?php echo gks_lang('Κοινωνικά Δίκτυα');?></label>
                  <?php echo gks_sociallinks_item('wp_users',$my_wp_user_id);?>
                  
                </div>
              </div>
                            

          
 
            </div>
          </div>
        </div>
      </div>
            
            
            
      <div class="accordion" id="accordion_address">
        <div class="card">
          <div class="card-header" id="a_address" style="background-color:#bfe7ed">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_address" aria-expanded="false" aria-controls="ac_address">
                <?php echo gks_lang('Διεύθυνση');?>
              </button>
            </h5>
          </div>
          <div id="ac_address" class="collapse" aria-labelledby="a_address" data-parent="#accordion_address" style="background-color:#bfe7ed">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_odos"><?php echo gks_lang('Οδός');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_odos" id="form_ma_odos" value="<?php echo htmlspecialchars_gks($row['ma_odos']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_arithmos"><?php echo gks_lang('Αριθμός');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_arithmos" id="form_ma_arithmos" value="<?php echo htmlspecialchars_gks($row['ma_arithmos']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_orofos"><?php echo gks_lang('Όροφος');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_orofos" id="form_ma_orofos" value="<?php echo htmlspecialchars_gks($row['ma_orofos']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_perioxi"><?php echo gks_lang('Περιοχή');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_perioxi" id="form_ma_perioxi" value="<?php echo htmlspecialchars_gks($row['ma_perioxi']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_poli"><?php echo gks_lang('Πόλη');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_poli" id="form_ma_poli" value="<?php echo htmlspecialchars_gks($row['ma_poli']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_tk"><?php echo gks_lang('Ταχυδρομικός Κώδικας');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ma_tk" id="form_ma_tk" value="<?php echo htmlspecialchars_gks($row['ma_tk']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>                
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_country_id"><?php echo gks_lang('Χώρα');?> (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_ma_country_id" id="form_ma_country_id" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <option value="0"><?php echo gks_lang('Χώρα');?>...</option>
                    <?php 
                    $countrys_ea_html='';
                    $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                    $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                    FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                    ".$lang_prepare_gks_country['sql']['from2']."
                    ORDER BY country_name";
                    
                    $result_country = $db_link->query($sql);
                    if (!$result_country) {
                      debug_mail(false,'error sql',$sql);
                      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
                    }
                    while ($row_country = $result_country->fetch_assoc()) {
                      echo '<option value="'.$row_country['id_country'].'" data-ci="'.$row_country['country_initials'].'" ';
                      if ($row_country['id_country'] == $row['ma_country_id']) echo ' selected ';
                      echo '>'.$row_country['country_name'].'</option>';
                      $countrys_ea_html.= '<option value="'.$row_country['id_country'].'">'.$row_country['country_name'].'</option>';
                    }
                    ?>                  
                  </select>
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_ma_nomos_id"><?php echo gks_lang('Νομός');?> (<span style="color:#00aa00">*</span>)</label>
                  <select name="form_ma_nomos_id" id="form_ma_nomos_id" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                    <?php 
                    if ($row['ma_country_id']>0) {
                      $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                      gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                      $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                      FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                      ".$lang_prepare_gks_nomos['sql']['from2']."
                      where country_id=".$row['ma_country_id']." ORDER BY nomos_descr";
                      
                      $result_nomos = $db_link->query($sql);
                      if (!$result_nomos) {
                        debug_mail(false,'error sql',$sql);
                        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
                      }
                      while ($row_nomos = $result_nomos->fetch_assoc()) {
                        echo '<option value="'.$row_nomos['id_nomos'].'"';
                        if ($row_nomos['id_nomos'] == $row['ma_nomos_id']) echo ' selected ';
                        echo '>'.$row_nomos['nomos_descr'].'</option>';
                      }
                    }
                    ?>                 
                  </select>
                </div>                
              </div>               
            </div>
          </div>
        </div>
      </div>
      
      <input type="hidden" value="" id="form_extra_address_delete" name="form_extra_address_delete" />
      
      <?php if (count($ea_a)>0) { ?>
     <div class="accordion" id="accordion_otheraddress">
        <div class="card">
          <div class="card-header" id="a_otheraddress" style="background-color:#b1d6db">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_otheraddress" aria-expanded="false" aria-controls="ac_otheraddress">
                <?php echo gks_lang('Άλλες διευθύνσεις');?>
              </button>
            </h5>
          </div>
          <div id="ac_otheraddress" class="collapse" aria-labelledby="a_otheraddress" data-parent="#accordion_otheraddress" style="background-color:#b1d6db">
            <div class="card-body">
              
              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <select name="form_select_apostoli" id="form_select_apostoli" class="form-control form-control-sm1 myneedsave" style="width:100%">
                    <?php 
                    foreach ($ea_a as $ea_item) {
                       echo '<option value="'.$ea_item['id'].'">'.$ea_item['name'].'</option>';
                    } 
                    ?>              
                  </select>                  
                </div>
              </div> 
              
              <div id="div_extra_address" style="display: none;">
                <div class="form-row">
                  <div class="form-group col-md-12" style="">
                    <p style="font-size:18pt;color:gray;margin:0px;"><?php echo gks_lang('Πληροφορίες αποστολής');?></p>
                  </div>                
                </div> 
              
                <div class="form-row">
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_name"><?php echo gks_lang('Όνομα Παραλήπτη');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_name" id="form_ea_name" value="">
                  </div>
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_phone"><?php echo gks_lang('Τηλέφωνο');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_phone" id="form_ea_phone" value="">
                  </div>                
                </div> 
                
                <div class="form-row">
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_odos"><?php echo gks_lang('Οδός');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_odos" id="form_ea_odos" value="">
                  </div>
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_arithmos"><?php echo gks_lang('Αριθμός');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_arithmos" id="form_ea_arithmos" value="">
                  </div>
                </div> 
                <div class="form-row">
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_orofos"><?php echo gks_lang('Όροφος');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_orofos" id="form_ea_orofos" value="">
                  </div>
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_perioxi"><?php echo gks_lang('Περιοχή');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_perioxi" id="form_ea_perioxi" value="">
                  </div>
                </div> 
                
                <div class="form-row">
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_poli"><?php echo gks_lang('Πόλη');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_poli" id="form_ea_poli" value="">
                  </div>
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_tk"><?php echo gks_lang('Ταχυδρομικός Κώδικας');?></label>
                    <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ea_tk" id="form_ea_tk" value="">
                  </div>                
                </div> 

                <div class="form-row">
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_country_id"><?php echo gks_lang('Χώρα');?></label>
                    <select name="form_ea_country_id" id="form_ea_country_id" class="form-control form-control-sm1 myneedsave">
                      <option value="0"><?php echo gks_lang('Χώρα');?>...</option>
                      <?php echo $countrys_ea_html;?>                  
                    </select>
                  </div>
                  <div class="form-group col-md-6" style="">
                    <label for="form_ea_nomos_id"><?php echo gks_lang('Νομός');?></label>
                    <select name="form_ea_nomos_id" id="form_ea_nomos_id" class="form-control form-control-sm1 myneedsave">
                      <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                    </select>
                  </div>                
                </div> 

                <div class="form-row">
                  <div class="form-group col-md-12" style="">
                    <button class="btn btn-danger" id="delete_extra_address"><?php echo gks_lang('Διαγραφή αυτής της διεύθυνσης');?></button>
                  </div>
                </div> 
              </div> 
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
          
      
      <div class="accordion" id="accordion_afmdoy">
        <div class="card">
          <div class="card-header" id="a_afmdoy" style="background-color:#97e7d4">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_afmdoy" aria-expanded="false" aria-controls="ac_afmdoy">
                <?php echo gks_lang('Φορολογικά');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>"><?php echo gks_lang('και Ασφαλιστικά');?></span> <?php echo gks_lang('στοιχεία');?>
              </button>
            </h5>
          </div>
          <div id="ac_afmdoy" class="collapse" aria-labelledby="a_afmdoy" data-parent="#accordion_afmdoy" style="background-color:#97e7d4">
            <div class="card-body">
              
              <div class="form-row">
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_arithmos_tautoitas"><?php echo gks_lang('Αριθμός Ταυτότητας');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_arithmos_tautoitas" id="form_arithmos_tautoitas" value="<?php echo htmlspecialchars_gks($row['arithmos_tautoitas']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_arxi_ekdosis"><?php echo gks_lang('Αρχή Έκδοσης');?> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_arxi_ekdosis" id="form_arxi_ekdosis" value="<?php echo htmlspecialchars_gks($row['arxi_ekdosis']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>                
              </div>               
                
              <div class="form-row">
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_amka"><span title="<?php echo gks_lang('Αριθμός Μητρώου Κοινωνικής Ασφάλισης');?>"><?php echo gks_lang('ΑΜΚΑ');?></span> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_amka" id="form_amka" value="<?php echo htmlspecialchars_gks($row['amka']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6 show_job_fields_class" style="<?php echo $show_job_fields_style;?>">
                  <label for="form_ama_eam"><span title="<?php echo gks_lang('Αριθμός Μητρώου Ασφαλισμένου');?>"><?php echo gks_lang('ΑΜΑ');?></span> - <span title="<?php echo gks_lang('Ενιαίος Αριθμός Μητρώου');?>"><?php echo gks_lang('ΕΑΜ');?></span> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_ama_eam" id="form_ama_eam" value="<?php echo htmlspecialchars_gks($row['ama_eam']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_eponimia"><?php echo gks_lang('Επωνυμία');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_eponimia" id="form_eponimia" value="<?php echo htmlspecialchars_gks($row['eponimia']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_title"><?php echo gks_lang('Τίτλος');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_title" id="form_title" value="<?php echo htmlspecialchars_gks($row['title']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>                        

              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_afm"><span title="<?php echo gks_lang('Αριθμός Φορολογικού Μητρώου');?>"><?php echo gks_lang('ΑΦΜ');?></span> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_afm" id="form_afm" value="<?php echo htmlspecialchars_gks($row['afm']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_doy"><span title="<?php echo gks_lang('Δημόσια Οικονομική Υπηρεσία');?>"><?php echo gks_lang('ΔΟΥ');?></span> <span class="show_job_fields_class" style="<?php echo $show_job_fields_style;?>">(<span style="color:#00aa00">*</span>)</span></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_doy" id="form_doy" value="<?php echo htmlspecialchars_gks($row['doy']); ?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε το * για πλήρη λίστα');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>   

              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_epaggelma"><?php echo gks_lang('Επάγγελμα');?></label>
                  <input type="text" class="form-control form-control-sm1 myneedsave" name="form_epaggelma" id="form_epaggelma" value="<?php echo htmlspecialchars_gks($row['epaggelma']); ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>   

            </div>
          </div>
        </div>
      </div>
            
      <div class="accordion show_job_fields_class" id="accordion_cvbio" style="<?php echo $show_job_fields_style;?>">
        <div class="card">
          <div class="card-header" id="a_cvbio" style="background-color:#b7e4ee">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_cvbio" aria-expanded="false" aria-controls="ac_cvbio">
                <?php echo gks_lang('Βιογραφικό');?>
              </button>
            </h5>
          </div>
          <div id="ac_cvbio" class="collapse" aria-labelledby="a_cvbio" data-parent="#accordion_cvbio" style="background-color:#b7e4ee">
            <div class="card-body">

              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_genisi_date"><?php echo gks_lang('Ημερομηνία Γέννησης');?> (<span style="color:#00aa00">*</span>)</label>
                  <input type="text" class="form-control form-control-sm1" name="form_genisi_date" id="form_genisi_date" value="<?php 
                          if (isset($row['genisi_date']) and $row['genisi_date'] !='') {
                            echo date('d/m/Y', strtotime($row['genisi_date']));
                          }
                          ?>" placeholder="<?php echo gks_lang('Ημερομηνία');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" >
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_genisi_date"><?php echo gks_lang('Ηλικία');?></label>
                  <p id="span_calc_age" style="font-style: italic;"></p>
                </div>
              </div> 
              
              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_description"><?php echo gks_lang('Σύντομο βιογραφικό');?> (<span style="color:#00aa00">*</span>)</label>
                  <textarea name="form_description" id="form_description" class="form-control form-control-sm1 myneedsave" style="height:200px" placeholder="<?php echo gks_lang('Μία μικρή περίληψη του βιογραφικού σας');?>"><?php echo get_user_meta($my_wp_user_id, 'description', true); ?></textarea>
                </div>
              </div> 

              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_ethnikotita"><?php echo gks_lang('Εθνικότητα');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Ελληνική');?>, <?php echo gks_lang('Ρωσική');?>, <?php echo gks_lang('Αλβανική');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['ethnikotita']); ?>" type="text" name="form_ethnikotita" id="form_ethnikotita" class="form-control form-control-sm1 myneedsave">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_alli_apasxolisi"><?php echo gks_lang('Άλλη Απασχόληση');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Καμία');?>, <?php echo gks_lang('Ιδιωτικός Υπάλληλος');?>, <?php echo gks_lang('Μερική απασχόληση');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['alli_apasxolisi']); ?>" type="text" name="form_alli_apasxolisi" id="form_alli_apasxolisi" class="form-control form-control-sm1 myneedsave" style="width:100%">
                </div>
              </div> 


              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_cv_proipiresia"><?php echo gks_lang('Προϋπηρεσία');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Καμία');?>, <?php echo gks_lang('Στον τάδε εργοδότη ως');?>...</i></small></label>
                  <textarea name="form_cv_proipiresia" id="form_cv_proipiresia" class="form-control form-control-sm1 myneedsave" style="height:200px"><?php echo htmlspecialchars_gks($row['cv_proipiresia']); ?></textarea>
                </div>
              </div>                         

              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_cv_spoydes"><?php echo gks_lang('Σπουδές');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Καμία');?>, <?php echo gks_lang('Λύκειο');?>, <?php echo gks_lang('ΤΕΙ τμήμα πληροφορικής');?></i></small></label>
                  <textarea name="form_cv_spoydes" id="form_cv_spoydes" class="form-control form-control-sm1 myneedsave" style="height:200px"><?php echo htmlspecialchars_gks($row['cv_spoydes']); ?></textarea>
                </div>
              </div>                         

              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <label for="form_cv_seminaria"><?php echo gks_lang('Σεμινάρια');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Κανένα');?>, <?php echo gks_lang('Η πρώτη μου φωτογραφία');?></i></small></label>
                  <textarea name="form_cv_seminaria" id="form_cv_seminaria" class="form-control form-control-sm1 myneedsave" style="height:200px"><?php echo htmlspecialchars_gks($row['cv_seminaria']); ?></textarea>
                </div>
              </div>                         

              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_cv_mitriki_glossa"><?php echo gks_lang('Μητρική Γλώσσα');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Ελληνικά');?>, <?php echo gks_lang('Αγγλικά');?>, <?php echo gks_lang('Γαλλικά');?>, <?php echo gks_lang('Ιταλικά');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['cv_mitriki_glossa']); ?>" type="text" name="form_cv_mitriki_glossa" id="form_cv_mitriki_glossa" class="form-control form-control-sm1 myneedsave">
                </div>
                <div class="form-group col-md-6" style="">
                  <label for="form_cv_jenes_glosses"><?php echo gks_lang('Ξένες Γλώσσες');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Καμία');?>, <?php echo gks_lang('Αγγλικά');?>, <?php echo gks_lang('Γαλλικά');?>, <?php echo gks_lang('Ιταλικά');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['cv_jenes_glosses']); ?>" type="text" name="form_cv_jenes_glosses" id="form_cv_jenes_glosses" class="form-control form-control-sm1 myneedsave" style="width:100%">
                </div>
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_cv_sxesi_me_photografia"><?php echo gks_lang('Σχέση με την Φωτογραφία');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('Καμία');?>, <?php echo gks_lang('Ερασιτεχνική');?>, <?php echo gks_lang('Σπουδές σε ΙΕΚ');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['cv_sxesi_me_photografia']); ?>" type="text" name="form_cv_sxesi_me_photografia" id="form_cv_sxesi_me_photografia" class="form-control form-control-sm1 myneedsave">
                </div>
                <div class="form-group col-md-6" style="">

                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group col-md-6" style="">
                  <label for="form_cv_metaforiko_meso"><?php echo gks_lang('Μεταφορικό Μέσο');?> (<span style="color:#00aa00">*</span>) <small><i><?php echo gks_lang('π.χ.');?> <?php echo gks_lang('ΜΜΜ');?>, <?php echo gks_lang('Παπάκι');?>, <?php echo gks_lang('Μηχανή');?>, <?php echo gks_lang('Αυτοκίνητο');?>, <?php echo gks_lang('Learjet');?></i></small></label>
                  <input value="<?php echo htmlspecialchars_gks($row['cv_metaforiko_meso']); ?>" type="text" name="form_cv_metaforiko_meso" id="form_cv_metaforiko_meso" class="form-control form-control-sm1 myneedsave" style="width:100%">
                </div>
              </div> 

              <div class="form-row">
                <div class="form-group col-md-12" style="">
    
                  <br>&nbsp;
                  <form role="form" method="post" action="profile-cv-upload.php" id="mycv_upload" enctype="multipart/form-data">
                    
                    <div class="form-group" id="imagelist_cv">
                    <?php echo gks_lang('Συνημμένα αρχεία π.χ. πλήρες βιογραφικό, συστάσεις, πτυχία, πιστοποιητικά κτλ.');?><br>
                    <?php   
                      $sql="select * from gks_users_cv where user_id=".$my_wp_user_id." and show_on_user_profile <> 0 order by id_user_cv";
                      $result_select = $db_link->query($sql);        
                      if (!$result_select) {
                        debug_mail(false,'error sql',$sql);
                        die('sql error');
                      }
                      while ($row_select = $result_select->fetch_assoc()) {
                        echo '<span id="item_upload_cv_' . $row_select['id_user_cv'] . '"><input type="text" class="input_item_upload_cv"
                        name="input_item_upload_cv_' . $row_select['id_user_cv'] . '" 
                        id="input_item_upload_cv_' . $row_select['id_user_cv'] . '" 
                        value="'.$row_select['file_descr'].'"
                        placeholder="'.gks_lang('Περιγραφή π.χ. Βιογραφικό').'"
                        style="width: 300px;max-width: 100%;"
                        title="'.gks_lang('Περιγραφή του αρχείου π.χ. Βιογραφικό, συστατική επιστολή, πτυχίο').'"
                        > <a href="'.$row_select['cv_url'].'" target="_blank">'.mb_basename($row_select['cv_url']).' ('.number_format($row_select['mysize']/1024/1024,2,',','.').' MB) </a> <a href="" class="delete_upload_cv" data-id="'.$row_select['id_user_cv'].'"><img src="/my/img/0.png" border="0" width="16"></a><br></span>';
                      }?>        
                    </div>
                    
                    
                    <div id="f_button_add_files_cv" class="fileinput-button"  href="#"     data-options="thumbnail: ''" style="padding-top:10px;width: 100%;text-align: center;">
                      <div id="f_button_add_files_cv_buttons">
                        <span style="position:relative;display: inline-block;">
                          <button type="submit" class="btn btn-sm btn-primary"><?php echo gks_lang('Μεταφόρτωση αρχείων');?></button>
                          <input type="file" name="files[]" multiple style="width: 100%;height: 100%;">    
                        </span>
                      </div>
                      <div id="f_button_add_files_cv_info" style="font-size: 0.875rem; ">
                        <?php echo gks_lang('Μέγιστο μέγεθος');?> <?php echo gks_get_max_upload_file_size();?>. <?php echo gks_lang('Τύποι αρχείων');?> pdf zip rar txt doc docx docm wps htm html odt sxw rtf
                      </div>
                    </div>
                    <div id="progress-bar_cv" style="margin-top:10px; display:none;background: rgb(230,230,230);">
                      <div class="bar_cv" style="padding-top:0px;padding-bottom:0px;width: 0%;height: 20px;background: green;"></div>
                    </div>
                    <div id="progress-extended_cv" style="display:none;">&nbsp;</div>
                    
                    
                  </form>   
                </div>
              </div> 
            </div>
          </div>
        </div>
      </div>
            
           
      <div class="accordion show_job_fields_class" id="accordion_bankac" style="<?php echo $show_job_fields_style;?>">
        <div class="card">
          <div class="card-header" id="a_bankac" style="background-color:#ffe2c3">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_bankac" aria-expanded="false" aria-controls="ac_bankac">
                <?php echo gks_lang('Τραπεζικοί λογαριασμοί');?>
              </button>
            </h5>
          </div>
          <div id="ac_bankac" class="collapse" aria-labelledby="a_bankac" data-parent="#accordion_bankac" style="background-color:#ffe2c3">
            <div class="card-body">
              
              <div class="form-row">
                <div class="form-group col-md-12" style="">

                          <table class="table table-striped table-bordered" border="0" cellspacing="0" cellpadding="0" id="table_bank_accounts">
                            <tr style="background-color: #eeeeee">
                              
                              <td width="5%" style="text-align: center;">
                                <i class="fas fa-trash-alt" style="text-align: center; color: #000000;font-size: 100%;" title="<?php echo gks_lang('Διαγραφή');?>"></i>
                              </td>                          
                              <td width="95%" style="text-align: left;"><?php echo gks_lang('Τραπεζικός Λογαριασμός');?> (<span style="color:#00aa00">*</span>)</td>                          
                            </tr>
                            <?php
                            $sql_bank_accounts="SELECT gks_bank_accounts.*, gks_banks.bank_descr
                            FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
                            WHERE gks_bank_accounts.deleted_from_user=0 AND gks_bank_accounts.user_id=".$my_wp_user_id."
                            ORDER BY gks_bank_accounts.id_bank_account";
                            $result_bank_accounts = $db_link->query($sql_bank_accounts);        
                            if (!$result_bank_accounts) {
                              debug_mail(false,'error sql',$sql_bank_accounts);
                              die('sql error');
                            }
                            $i = 0;
                            while ($row_bank_accounts = $result_bank_accounts->fetch_assoc()) { 
                              $i++;
                              ?>
                            <tr id="rowbankaccount-<?php echo $row_bank_accounts['id_bank_account'];?>" style="background-color: white;">
                              <td style="text-align: center;">
                                <i id="delrec-<?php echo $row_bank_accounts['id_bank_account'];?>" class="mybankaccountdelete fas fa-trash-alt" style="cursor: pointer;text-align: center; color: #ff0000; font-size: 100%;" title="<?php echo gks_lang('Διαγραφή');?>"></i>
                                
                              </td>
                              
                              <td style="text-align: left;"><?php
                                echo gks_lang('IBAN').': ';
                                
                                $iban = iban_to_machine_format($row_bank_accounts['IBAN']);
                                
                                if(verify_iban($iban)) {
                                  echo iban_to_human_format($iban);
                                } else {
                                  echo $row_bank_accounts['IBAN'];
                                }
                                
                                echo '<br>'.
                                gks_lang('Τράπεζα').': '.$row_bank_accounts['bank_descr'].'<br>'.
                                gks_lang('Δικαιούχος').': '.$row_bank_accounts['account_dikaiouxos'];
                                
                              ?>
                              </td>                            
                            </tr>
                          
                            <?php }?> 
                          </table>



                </div>
              </div>  
                           
              <div class="form-row">
                <div class="form-group col-md-12" style="">
                  <button id="addbankaccount" class="btn btn-primary">
                    <i class="fas fa-plus-circle" style="cursor: pointer;font-size: 100%;"></i>
                    <?php echo gks_lang('Προσθήκη');?>
                  </button>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>  
      
      
      <div class="accordion" id="accordion_newslt">
        <div class="card">
          <div class="card-header" id="a_newslt" style="background-color:#d8f0c1">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed gks_accordion_link" type="button" data-toggle="collapse" data-target="#ac_newslt" aria-expanded="false" aria-controls="ac_newslt">
                <?php echo gks_lang('Newsletter');?>
              </button>
            </h5>
          </div>
          <div id="ac_newslt" class="collapse" aria-labelledby="a_newslt" data-parent="#accordion_newslt" style="background-color:#d8f0c1">
            <div class="card-body">
                            
                       <?php $sql="";
                            $nl_emails=array();
                            if ($user_email!='') {
                              $sql_nl_emails="select * from gks_newsletter_emails where myemail like '".$db_link->escape_string($user_email)."'";
                              $result_nl_emails = $db_link->query($sql_nl_emails);        
                              if (!$result_nl_emails) {
                                debug_mail(false,'error sql',$sql_nl_emails);
                                die('sql error');
                              }
                              while ($row_nl_emails = $result_nl_emails->fetch_assoc()) {
                                $nl_emails[$row_nl_emails['newsletter_list_id']] = $row_nl_emails['isapproval'];
                              }
                            }
                            $nl_sms=array();
                            if ($user_mobile!='') {
                              $sql_nl_sms="select * from gks_newsletter_sms where mysms like '".$db_link->escape_string($user_mobile)."'";
                              $result_nl_sms = $db_link->query($sql_nl_sms);        
                              if (!$result_nl_sms) {
                                debug_mail(false,'error sql',$sql_nl_sms);
                                die('sql error');
                              }
                              while ($row_nl_sms = $result_nl_sms->fetch_assoc()) {
                                $nl_sms[$row_nl_sms['newsletter_list_id']] = $row_nl_sms['isapproval'];
                              }
                            }
                            
                            
                            $sql_newsletter_lists="select * from gks_newsletter_lists where newsletter_list_disabled=0 order by id_newsletter_list";
                            $result_newsletter = $db_link->query($sql_newsletter_lists);        
                            if (!$result_newsletter) {
                              debug_mail(false,'error sql',$sql_newsletter_lists);
                              die('sql error');
                            }
                            $i = 0;
                            while ($row_newsletter_lists = $result_newsletter->fetch_assoc()) { 
                              $i++;          
                              ?>

                              <div class="form-row">
                                <div class="form-group col-md-6">
                                <?php echo $row_newsletter_lists['newsletter_list_title'];?>
                                </div>
                                <div class="form-group col-md-3">
                                  <input type="checkbox" name="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" id="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" class="newsletter-email" data-id="<?php echo $row_newsletter_lists['id_newsletter_list'];?>"
                                  <?php if (isset($nl_emails[$row_newsletter_lists['id_newsletter_list']]) and $nl_emails[$row_newsletter_lists['id_newsletter_list']] == 1) {
                                    echo " checked ";;
                                  } ?>
                                  >
                                  <label for="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" style="display:inline">email</label>
                                
                                </div>
                                <div class="form-group col-md-3">
                                  <input type="checkbox" name="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" id="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" class="newsletter-sms" data-id="<?php echo $row_newsletter_lists['id_newsletter_list'];?>"
                                  <?php if (isset($nl_sms[$row_newsletter_lists['id_newsletter_list']]) and $nl_sms[$row_newsletter_lists['id_newsletter_list']] == 1) {
                                    echo " checked ";;
                                  } ?>
                                  >
                                  <label for="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" style="display:inline">SMS</label>
                                  
                                </div>
                              </div>
                        
                        <?php } ?>              


            </div>
          </div>
        </div>
      </div>  
      
      

      
    
      
                
    </div>
  </div>
  
  
  <div class="row" style="padding-top: 24px;<?php if ($show_job_fields) echo 'display:none;'?>">
    <div class="col-sm-12">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><?php echo gks_lang('Για μελλοντικούς εργαζόμενους');?></h5>
          <p class="card-text"><a href="/jobs/" target="_blank"><?php echo gks_lang('Διαβάστε περισσότερα');?></a></p>
          <input type="checkbox" name="show_job_fields_checkbox"  id="show_job_fields_checkbox" value="1"   <?php if ($show_job_fields) echo ' checked '; ?> > 
          <label for="show_job_fields_checkbox" style="display:inline"> <?php echo gks_lang('Προβολή επιπλέον πεδίων βιογραφικού');?> </label>
        </div>
      </div>
    </div>
  </div>  


  <div class="row" style="padding-top: 24px;">
    <div class="col-sm-12">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><?php echo gks_lang('Ποσοστό συμπλήρωσης του προφίλ');?></h5>
          <div class="progress" style="height: 26px;margin-bottom: 24px;">
            <div id="myprofilepososto" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div id="reqfields"></div>
          <div class="text-left">
            <p>(<span style="color:#ff0000">*</span>)&nbsp;<?php echo gks_lang('Απαραίτητα πεδία');?>
              <br>(<span style="color:#00aa00">*</span>)&nbsp;<?php echo gks_lang('Συμμετέχει στο ποσοστό συμπλήρωσης του προφίλ');?></p>
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

      <button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="mysave"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" style="margin-bottom:10px;" class="btn btn-danger deleterowbtn" id="back_to_home"><?php echo gks_lang('Άκυρο');?></button>
      
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<div id="dialog_add_bank_account" title="<?php echo gks_lang('Προσθήκη νέου τραπεζικού λογαριασμού');?>" style="display: none;">
  <div style="padding: 20px;">
  <p ><?php echo gks_lang('Εισάγετε τον τραπεζικό λογαριασμό σας');?></p>
  <div class="row" style="padding-top: 2px;">
    <div class="col-md-12">
      <div class="form-group row" id="selecttypediv2" style="">
        <label for="dialog_add_bank_account_iban" class="col-md-4 col-form-label form-control-sm text-md-left"><?php echo gks_lang('ΙΒΑΝ');?>:</label>
        <div class="col-md-8">
            <input type="text" name="dialog_add_bank_account_iban" id="dialog_add_bank_account_iban" value="" class="form-control form-control-sm" style="width:90%">
        </div>
      </div>
      <div class="form-group row" id="selecttypediv2" style="">
        <label for="dialog_add_bank_account_bank_id" class="col-md-4 col-form-label form-control-sm text-md-left"><?php echo gks_lang('Τράπεζα');?>:</label>
        <div class="col-md-8">
          <select name="dialog_add_bank_account_bank_id" id="dialog_add_bank_account_bank_id" class="form-control form-control-sm" style="width:90%">
          <option value="0"></option>
          <?php
          $sql="select * FROM gks_banks ORDER BY bank_descr";
          $result_select = $db_link->query($sql);        
          if (!$result_select) {
            debug_mail(false,'error sql',$sql);
            die('sql error');
          }
          while ($row_select = $result_select->fetch_assoc()) {
            echo '<option value="'.$row_select['id_bank'].'" ';
            echo '>'.$row_select['bank_descr'].'</option>';
          }?></select>             
            
        </div>
      </div>
      <div class="form-group row" id="selecttypediv2" style="">
        <label for="dialog_add_bank_account_dikaiouxos" class="col-md-4 col-form-label form-control-sm text-md-left"><?php echo gks_lang('Δικαιούχος');?>:</label>
        <div class="col-md-8">
            <input type="text" name="dialog_add_bank_account_dikaiouxos" id="dialog_add_bank_account_dikaiouxos" value="" class="form-control form-control-sm" style="width:90%">
        </div>
      </div>
      
      
    </div>
  </div>
  <center>
  <img id="dialog_add_bank_account_progressbar" src="/my/img/progress_bar2.gif"> 
  </center>
  </div>
</div>
    
    




<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

  
var from_php_id=<?php echo $my_wp_user_id;?>;

var from_php_js_profilepososto_user = <?php echo $profilepososto_user;?>;
var from_php_js_profilepososto_job  = <?php echo $profilepososto_job;?>;
var from_php_country_id = <?php echo $row['ma_country_id'];?>;

<?php 
echo $ea_j; //extra_address
?>
var from_php_first_ea_j = <?php echo $first_ea_j;?>;



jQuery(document).ready(function($) {     
  <?php include_once('_dialogs.js.php'); ?>


});


</script>

<script src="js/profile.js?v=<?php echo $gks_cache_version;?>"></script>

<link rel="stylesheet" href="/my/css/_gks_filesobjectlist.css?v=<?php echo $gks_cache_version;?>" type="text/css">  
<link rel="stylesheet" href="/my/js/jquery.fileupload/jquery.fileupload.css" type="text/css">    
<script src="/my/js/jquery.fileupload/vendor/jquery.ui.widget.js"></script>
<script src="/my/js/jquery.fileupload/jquery.iframe-transport.js"></script>
<script src="/my/js/jquery.fileupload/jquery.fileupload.js"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-process.js"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-validate.js"></script> 
 



 
<?php
include_once('_my_footer_admin.php');
db_close();

