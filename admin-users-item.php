<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


//echo '<pre>';print_r($gks_user_settings);die();
$gks_voip_params=gks_voip_user_params();
//echo '<pre>';print_r($gks_voip_params);die();

/*
$ddd=[];
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'nipt/NIPT TRF Request Consent Form_ ENG FINAL.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'nipt/Prequel_NIPT Request Consent Form_ for laboratory ENG FINAL.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'nipt/prequel_report_eng_SHRO.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'nipt/Prequel Flyer_2025.pdf','name_for_email'=>'');
$ddd[]=array('def_check'=>true,'basefolder'=>'erpfi','relative_path'=>'nipt/Prequel_Payment instructions.pdf','name_for_email'=>'');
echo '<pre>';print(json_encode($ddd));die();
*/
//$perm_ret=gks_permission_get_user(3);
//print '<pre>';print_r($perm_ret);die();


//$gks_wsl_current_user_image='https://test.easyfilesselection.com/my/uploads/users-photo/20430/2021/01/20/thumbnail/shutterstock_10993705.jpg';
//if ($gks_wsl_current_user_image!='' and endwith(strtolower($gks_wsl_current_user_image), '.jpg')) {
//  if (startwith($gks_wsl_current_user_image,GKS_SITE_URL)) {
//    $gks_wsl_current_user_image= GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/'.substr($gks_wsl_current_user_image, strlen(GKS_SITE_URL));
//    if (file_exists($gks_wsl_current_user_image)) {
//      $tmp_file=GKS_SITE_PATH.'tmp/'.rand(1000,9999).'.jpg';
//      makeThumbnails_square($gks_wsl_current_user_image,$tmp_file,100,false);
//      //makeThumbnails_normal($gks_wsl_current_user_image,$tmp_file,100,100,false);
//      if (file_exists($tmp_file)) {
//        $gks_wsl_current_user_image=base64_encode(file_get_contents($tmp_file));
//        echo $gks_wsl_current_user_image;
//      }
//    }
//
//  }
//  
//  
//}
//die();



$show_admin=false;
$show_hr=false;
$show_lo=false;

$show_others=false;
if (ur_ad()) {
  $show_admin=true;
} else if (ur_hr()) {
  $show_hr = true;
} else if (ur_lo()) {
  $show_lo=true;
} else {
  $show_others=true;
}










if ($id==-1) {
  $nav_active_array=array('manage','manage_new_user'); 
} else {
  $nav_active_array=array('manage','manage_users');
}

$gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'item']);


if ($id==-1) {
  $sql="select max(id) as cc from ".GKS_WP_TABLE_PREFIX."users";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'admin-users-item.php error sql',$sql);
    die('sql error');
  }  
  $row = $result->fetch_assoc();
  $maxid=$row['cc'] + 1;


  $row=array();

  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';

  
  $row['ID']=-1;
  $row['user_login']=gks_lang('Επαφή').$maxid;
  $row['user_nicename']=gks_lang('Επαφή').$maxid;
  $row['display_name']=gks_lang('Επαφή').$maxid;
  $row['gks_nickname']=gks_lang('Επαφή').$maxid;
  $row['user_pass']='';
  $row['user_pass_pure']='';
  $row['user_email']='';
  $row['user_url']='';
  $user_roles = array('subscriber');
  $row['mymoobile']='';
  $row['profilepososto_user']=0;
  $row['profilepososto_job']=0;  
  $row['mylast_name']='';
  $row['myfirst_name']='';
  $row['onoma_patera']='';
  $row['onoma_miteras']='';
  $row['gks_sex']=0;
  $row['gks_lang']='el-GR';
  $row['phone_home']='';
  $row['viber_id']='';
  $row['viber_subscribed']=0;
  $row['ma_branch']='';
  $row['ma_odos']='';
  $row['ma_arithmos']='';
  $row['ma_orofos']='';
  $row['ma_perioxi']='';
  $row['ma_poli']='';
  $row['ma_tk']='';
  $row['ma_country_id']=91;
  $row['ma_nomos_id']=0;
  $row['user_pin']='';
  $row['old_code']='';
  $row['user_HumanInitial']='';
  $row['order_sxolio']='';
  $row['pelati_sxolio']='';
  
  $row['amka']='';
  $row['ama_eam']='';
  $row['arithmos_tautoitas']='';
  $row['arxi_ekdosis']='';
  $row['fiscal_position_id']=1;
  $row['pricelist_id']=1;
  $row['generic_ekprosi']=0;
  $row['job_title']='';
  $row['eponimia']='';
  $row['title']='';
  $row['afm']='';
  $row['doy']='';
  $row['epaggelma']='';

  $row['gemi_number']='';
  $row['is_b2g']=0;
  $row['b2g_aaht_code']='';
  $row['b2g_aaht_name']='';
  $row['b2g_aaht_foreas']='';
  $row['b2g_aaht_typos_forea']='';
  $row['b2g_aaht_kodikos_ekatharisis']='';

  $row['oikogeniaki_katastasti_id']=0;
  $row['oikogeniaki_katastasti_paidia']=0;

  $row['genisi_date']=null;
  $row['ethnikotita']=gks_lang('Ελληνική');
  $row['alli_apasxolisi']='';
  $row['cv_proipiresia']='';
  $row['cv_spoydes']='';
  $row['cv_seminaria']='';
  $row['cv_mitriki_glossa']=gks_lang('Ελληνικά');
  $row['cv_jenes_glosses']='';
  $row['cv_sxesi_me_photografia']='';
  $row['cv_metaforiko_meso']='';
  $row['cv_has_bike']=0;
  $row['cv_has_motorcycle']=0;
  $row['cv_has_car']=0;
  $row['cv_has_car_theseis']=0;

  $row['sistasi_from']='';
  $row['days_to_work']=null;
  $row['gks_last_update']=null;
  
//  $row['mydate_add']=
//  $row['mydate_edit']=
//  $row['user_id_add']=0
//  $row['user_id_edit']=0
//  $row['myip']=

  $row['ma_latitude']='';
  $row['ma_longitude']='';
  
  

  $user_communication=array();
  //echo $id;die();

  
} else {
  //$calc = calc_profilepososto($id,false);
  $user_object = new WP_User($id);
  $user_roles=(array)$user_object->roles;
  
//  print '<pre>';
//  var_dump($user_roles);
//  print_r($user_roles);
//  print '</pre>';
//  die();
  
  
  
  $sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
  gks_users.gemi_number,gks_users.is_b2g,gks_users.b2g_aaht_code,gks_users.b2g_aaht_name,
  gks_users.b2g_aaht_foreas,gks_users.b2g_aaht_typos_forea,gks_users.b2g_aaht_kodikos_ekatharisis,
  gks_users.ma_branch,
  gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
  gks_users.ma_poli, gks_users.ma_tk, 
  gks_users.ma_country_id, gks_users.ma_nomos_id, 
  gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
  gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.order_sxolio,gks_users.pelati_sxolio, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
  gks_users.cv_sxesi_me_photografia, gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,gks_users.cv_has_car_theseis,
  gks_users.profilepososto_user, gks_users.profilepososto_job,
  gks_country.country_name, gks_nomoi.nomos_descr, 
  table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
  gks_users.user_HumanInitial,
  gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
  gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia,
  gks_users.sistasi_from,gks_users.days_to_work,
  gks_users.ma_latitude,gks_users.ma_longitude,
  gks_users.job_title
  FROM (((((((((((".GKS_WP_TABLE_PREFIX."users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on ".GKS_WP_TABLE_PREFIX."users.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on ".GKS_WP_TABLE_PREFIX."users.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
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
  where ".GKS_WP_TABLE_PREFIX."users.id = ".$id;
  
  
  
  if ($show_others) {
  	$sql.= " and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('a:1:{s:10:"subscriber";b:1;}')."'";
  }
  if (ur_ad() == false) {
    $sql.= " and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%administrator%')."'
             and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%adminmy%')."'";
  }
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'admin-users-item.php error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'admin-users-item.php record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  //print '<pre>';print_r($row);die();
  $row['user_nicename'] = get_user_meta($id, 'nickname', true);

  
  $user_communication=array();
  if (trim_gks($row['user_email'])!='') {
    $user_communication['email'][$row['user_email']]=array('descr' => '','isp' => 0);
  }
  if (trim_gks($row['mymoobile'])!='') {
    $user_communication['phone'][$row['mymoobile']]=array('descr' => '','isp' => 0);
  }
  if (trim_gks($row['phone_home'])!='') {
    $user_communication['phone'][$row['phone_home']]=array('descr' => '','isp' => 0);
  }
  if (trim_gks($row['user_url'])!='') {
    $user_communication['url'][$row['user_url']]=array('descr' => '','isp' => 0);
  }
  
  
  
  $sql_comm="select * from gks_users_communication where user_id=".$id." order by id_user_communication";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
  while ($row_comm = $result_comm->fetch_assoc()) {
    if (isset($user_communication[$row_comm['comm_type']][$row_comm['comm_value']])==false) {
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]=array('descr' => $row_comm['comm_descr'],'isp' => $row_comm['comm_primary']);
    } else {
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]['descr']=$row_comm['comm_descr'];
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]['isp']=$row_comm['comm_primary'];
    }
  }
  

}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

if (isset($user_communication['email'])==false) $user_communication['email']['']=array('descr' => '','isp' => 1);
if (isset($user_communication['phone'])==false) $user_communication['phone']['']=array('descr' => '','isp' => 1);
if (isset($user_communication['url'])==false)   $user_communication['url']['']=array('descr' => '','isp' => 1);



$user_email = $row['user_email'];
$user_mobile = $row['mymoobile'];
$user_login= $row['user_login'];
$user_pass_pure = $row['user_pass_pure'];

$profilepososto_user = $row['profilepososto_user'];
$profilepososto_job = $row['profilepososto_job'];


if (!isset($row['ma_country_id'])) $row['ma_country_id']=91;
if (!isset($row['ma_nomos_id'])) $row['ma_nomos_id']=0;



$cansendpassword=false;
if ($id>0 and $row['user_pass_pure']!='' and $row['user_pass']!= '' and wp_check_password($row['user_pass_pure'], $row['user_pass'], $id)) {
  $cansendpassword=true;
}


$object_title='';
if (isset($row['gks_nickname'])) {$object_title=$row['gks_nickname']; $my_page_title=gks_lang('Επαφή').': '.$object_title;}
else if (isset($row['mylast_name'])) {$object_title=$row['mylast_name'].' '.$row['myfirst_name']; $my_page_title=gks_lang('Επαφή').': '.$object_title  ;}
else $my_page_title=gks_lang('Επαφή').': '.$id;

stat_record();

include_once('_my_header_admin.php');
?>

<link href="css/admin-users-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-6" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Επαφή');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Επαφή');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>

    </div>
    <div class="col-md-6" style="text-align:center">
      <?php if ($id>0) {?>
      
      
      <a class="btn btn-primary" href="admin-users-item-card.php?id=<?php echo $id;?>"><?php echo gks_lang('Οικονομική Καρτέλα');?></a>
      <a class="btn btn-primary" href="admin-users-item-overview.php?id=<?php echo $id;?>"><?php echo gks_lang('Επισκόπηση');?></a>

      <?php if ($show_admin or $show_lo) { ?>
        <button style="justify-content: center!important;" type="button" class="btn btn-primary gks_export_word"
          onclick="window.location.href='admin-users-item-export-logistis.php?id=<?php echo $id;?>'"  <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Εξαγωγή σε Word');?></button>
      <?php } ?>

      <?php } ?>
    </div>
  </div>
</div>

<div id="mypostform">
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>        
 
 
          <div class="form-group row">
            <label for="" class="col-sm-4 col-form-label form-control-sm text-sm-right"><a class="tooltipster" title="<?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?>" href="https://www.aade.gr/epiheiriseis/forologikes-ypiresies/mitroo/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon" target="_blank"><?php echo gks_lang('aade.gr');?></a>:</label>
            <div class="col-sm-8">
              <button style="" id="btn_gsis_get" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση με το ΑΦΜ');?></button>
              <button style="" id="btn_vies_get" class="btn btn-sm btn-primary"><?php echo gks_lang('VIES');?></button>
              
            </div>
          </div>
               
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8">
              <input type="text" readonly class="form-control-plaintext form-control-sm myneedsave" value="<?php echo $row['ID'];?>">
            </div>
          </div>
               
          <div class="form-group row">
            <label for="user_login" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα χρήστη');?>:</label>
            <div class="col-md-8">
              <input id="user_login" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_login']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="myfirst_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="myfirst_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['myfirst_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="mylast_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επίθετο');?>:</label>
            <div class="col-md-8">
              <input id="mylast_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['mylast_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="user_nicename" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Υποκοριστικό');?>:</label>
            <div class="col-md-8">
              <input id="user_nicename" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_nicename']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="display_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προβολή δημοσίως ως');?>:</label>
            <div class="col-md-8">
              <select id="display_name"  class="form-control form-control-sm myneedsave">
                <option <?php if ($row['display_name'] == $row['user_login']) echo ' selected ';?>><?php echo $row['user_login'];?></option>
                <?php if ($row['user_nicename'] !='') {?> <option <?php if ($row['display_name'] == $row['user_nicename']) echo ' selected ';?>><?php echo $row['user_nicename'];?></option>  <?php } ?>
                <?php if ($row['myfirst_name']  !='') {?> <option <?php if ($row['display_name'] == $row['myfirst_name']) echo ' selected ';?>><?php echo $row['myfirst_name'];?></option> <?php } ?>
                <?php if ($row['mylast_name']   !='') {?> <option <?php if ($row['display_name'] == $row['mylast_name']) echo ' selected ';?>><?php echo $row['mylast_name'];?></option> <?php } ?>
                <?php if ($row['myfirst_name']  !='') {?> <option <?php if ($row['display_name'] == $row['myfirst_name'].' '.$row['mylast_name']) echo ' selected ';?>><?php echo $row['myfirst_name'].' '.$row['mylast_name'];?></option> <?php } ?>
                <?php if ($row['mylast_name']   !='') {?> <option <?php if ($row['display_name'] == $row['mylast_name'].' '.$row['myfirst_name']) echo ' selected ';?>><?php echo $row['mylast_name'].' '.$row['myfirst_name'];?></option> <?php } ?>
              </select>    
            </div>
          </div>
                              
          <div class="form-group row">
            <label for="job_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος θέσης');?>:</label>
            <div class="col-md-8">
              <input id="job_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['job_title']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="gks_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-md-8">
              <select id="gks_lang" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
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
                  if ($row_select['id_lang']==$row['gks_lang']) echo ' selected ';
                  echo '>'.$row_select['lang_name'].'</option>';
                }?>
              </select>    
            </div>
          </div>      

      
        </div>
      </div>

            
 

      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Στοιχεία επικοινωνίας');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('conta');?>>        
          
          
          <div class="form-group row">
            <label class="gks_comm_email_label col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('email');?>:</label>
            <div class="col-md-8" id="gks_comm_email_cont_div">
            <?php 
            $comm_aa=0;
            foreach ($user_communication['email'] as $com_value => $com_data) {
               $comm_aa++;?>
              <div class="row gks_comm_email_div" data-aa="<?php echo $comm_aa;?>">
                <div class="col-md-6">
                  <input type="text" class="gks_comm_email_value form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($com_value);?>" placeholder="<?php echo gks_lang('π.χ. info@gks.gr');?>"  autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <i class="fas fa-envelope gks_comm_email_primary <?php 
                    if ($com_data['isp']!=0) echo 'gks_comm_email_primary_sel';
                    ?>" data-aa="<?php echo $comm_aa;?>" title="<?php
                    if ($com_data['isp']!=0) echo gks_lang('Προεπιλογή'); else gks_lang('Ορισμός ως προεπιλογή');
                    ?>"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" class="gks_comm_email_descr form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($com_data['descr']);?>" placeholder="<?php echo gks_lang('π.χ. Εργασίας');?>"  autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <i class="fas fa-trash-alt gks_comm_email_delete" data-aa="<?php echo $comm_aa;?>"></i>
                  <i class="fas fa-plus-circle gks_comm_email_add" data-aa="<?php echo $comm_aa;?>" style="<?php 
                    if (count($user_communication['email']) != $comm_aa) echo 'display:none;';?>"></i>
                </div>
              </div>
            <?php }   ?>                     
            </div>
          </div>
          
          <div class="form-group row">
            <label class="gks_comm_phone_label col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8" id="gks_comm_phone_cont_div">
            <?php 
            $comm_aa=0;
            foreach ($user_communication['phone'] as $com_value => $com_data) {
               $comm_aa++;?>
              <div class="row gks_comm_phone_div" data-aa="<?php echo $comm_aa;?>">
                <div class="col-md-6">
                  <input type="text" class="gks_comm_phone_value form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" value="<?php echo htmlspecialchars_gks($com_value);?>" placeholder="<?php echo gks_lang('π.χ. 6912345678');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <?php echo $gks_voip_params['html_after_input'];?>
                  <i class="fas fa-phone gks_comm_phone_primary <?php 
                    if ($com_data['isp']!=0) echo 'gks_comm_phone_primary_sel';
                    ?>" data-aa="<?php echo $comm_aa;?>" title="<?php
                    if ($com_data['isp']!=0) echo gks_lang('Προεπιλογή'); else echo gks_lang('Ορισμός ως προεπιλογή');
                    ?>"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" class="gks_comm_phone_descr form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($com_data['descr']);?>" placeholder="<?php echo gks_lang('π.χ. Κινητό');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <i class="fas fa-trash-alt gks_comm_phone_delete" data-aa="<?php echo $comm_aa;?>"></i>
                  <i class="fas fa-plus-circle gks_comm_phone_add" data-aa="<?php echo $comm_aa;?>" style="<?php 
                    if (count($user_communication['phone']) != $comm_aa) echo 'display:none;';?>"></i>
                </div>
              </div>
            <?php }   ?>                     
            </div>
          </div>
          
          <div class="form-group row">
            <label class="gks_comm_url_label col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8" id="gks_comm_url_cont_div">
            <?php 
            $comm_aa=0;
            foreach ($user_communication['url'] as $com_value => $com_data) {
               $comm_aa++;?>
              <div class="row gks_comm_url_div" data-aa="<?php echo $comm_aa;?>">
                <div class="col-md-6">
                  <input type="text" class="gks_comm_url_value form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($com_value);?>" placeholder="<?php echo gks_lang('π.χ. www.gks.gr');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <i class="fas fa-link gks_comm_url_primary <?php 
                    if ($com_data['isp']!=0) echo 'gks_comm_url_primary_sel';
                    ?>" data-aa="<?php echo $comm_aa;?>" title="<?php
                    if ($com_data['isp']!=0) echo gks_lang('Προεπιλογή'); else echo gks_lang('Ορισμός ως προεπιλογή');
                    ?>"></i>
                </div>
                <div class="col-md-6">
                  <input type="text" class="gks_comm_url_descr form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($com_data['descr']);?>" placeholder="<?php echo gks_lang('π.χ. Εταιρικό site');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                  <i class="fas fa-trash-alt gks_comm_url_delete" data-aa="<?php echo $comm_aa;?>"></i>
                  <i class="fas fa-plus-circle gks_comm_url_add" data-aa="<?php echo $comm_aa;?>" style="<?php 
                    if (count($user_communication['url']) != $comm_aa) echo 'display:none;';?>"></i>
                </div>
              </div>
            <?php }   ?>                     
            </div>
          </div>                                    
          

          <div class="form-group row">
            <label for="viber_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Viber ID');?>:</label>
            <div class="col-md-8">
              <input id="viber_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['viber_id']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <div style="margin-top: 4px;">
              <img src="img/<?php echo ($row['viber_subscribed'] == 1 ? '1' : '0');?>.png" border="0" width="16" style="width:24px;">
              <button class="btn btn-sm btn-primary" id="viber_send_def_text"><?php echo gks_lang('Αποστολή δοκιμαστικού κειμένου');?></button>    
              </div>
            </div>
          </div> 
          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
          <?php echo gks_sociallinks_item('wp_users',$id);?>
        </div>
      </div>
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Διεύθυνση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('addr');?>>        

          <div class="form-group row">
            <label for="ma_branch" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-md-8">
              <input id="ma_branch" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['ma_branch'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min=0>
            </div>
          </div> 
          <div class="form-group row">
            <label for="ma_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="ma_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_odos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted auto_googlemaps" id="ma_odos_auto_googlemaps"></small>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="ma_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="ma_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_arithmos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ma_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="ma_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_orofos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="ma_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="ma_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_perioxi']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          
          
          
          <div class="form-group row">
            <label for="ma_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="ma_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_poli']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ma_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('TK');?>:</label>
            <div class="col-md-8">
              <input id="ma_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_tk']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ma_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="ma_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['ma_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['ma_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>            
          <div class="form-group row">
            <label for="ma_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="ma_country_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                ".$lang_prepare_gks_country['sql']['from2']."
                ORDER BY country_name";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                  if ($row_select['id_country']==$row['ma_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
                 
          
          <div class="form-group row">
            <label for="ma_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="ma_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ma_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="ma_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χάρτης');?>:</label>
            <div class="col-md-8">
              <div style="text-align:left;">
                <button id="showmap" class="btn btn-sm btn-primary" style="cursor:pointer"><?php echo gks_lang('Εμφάνιση χάρτη');?></button>
                <button id="geocode_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled><?php echo gks_lang('Στίγμα');?> <span id="geocode_pos_icon"><i class="fas fa-map-marker-alt"></i></span></button>
                <button id="map_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled title="<?php echo gks_lang('Εντοπισμός της τρέχουσας θέσης σας');?>"><?php echo gks_lang('Εδώ');?></button>
                
                </div>
            </div>
            <div class="col-md-12" style="height:0px">
              <div id="map" style="width:100%;height:100%"></div>  
            </div>             
          </div>                  
          
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <span class="btn btn-sm btn-primary gks_stoppropagation" style="margin-left:10px;" id="gks_card_extra_address_btn"><?php echo gks_lang('Άλλες διευθύνσεις της επαφής');?></span>
            </div>
          </div>
          
        </div>
      </div>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>

      <?php if ($show_admin or $show_hr or $show_lo) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κωδικός πρόσβασης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('pass');?>>         
      
     
          <div class="form-group row">
            <label for="user_pass_pure" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νέο συνθηματικό');?>:</label>
            <div class="col-md-8">
              <input id="user_pass_pure" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <div class="gks_td0875">
              <?php
              if (ur_ad() or (ur_lo() and (!(
                isset($user_roles['administrator']) or 
                isset($user_roles['adminmy']) or 
                isset($user_roles['logistis'])
                )))) {
                echo '<a href="admin-users-item-login-other.php?id='.$id.'">'.gks_lang('Σύνδεση ως ...').'</a>';  
              }
              if ($cansendpassword==true) {
                echo '<br>'.gks_lang('Παλιό password').': <span class="old_password_from_db">'. $row['user_pass_pure'].'</span>';
              } else {
                echo '<br>'.gks_lang('Παλιό password : Άγνωστο').'<span class="old_password_from_db"></span>'; 
              } ?>
              </div>
            </div>
          </div> 

        </div>
      </div>

      <?php } ?> 

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιδιότητες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('idio');?>>         
      
          <div class="form-group row">
            <label for="user_pin" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('PIN');?>:</label>
            <div class="col-md-8">
              <input id="user_pin" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_pin']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>  
          <div class="form-group row">
            <label for="old_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παλιός Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="old_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['old_code']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>  
          <div class="form-group row">
            <label for="user_HumanInitial" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός Υπαλλήλου');?>:</label>
            <div class="col-md-8">
              <input id="user_HumanInitial" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_HumanInitial']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>  
               
          
               
               
        </div>
      </div>


    </div>    
      
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φωτογραφίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('photos');?>>       
          <div class="row gks_td0875">
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία της επαφής');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              
              $user_photo_value="";
              $myimgurl = ($id>0 ? get_user_meta($id, 'wsl_current_user_image', true) : '');
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
            </div>                     
          </div>
          <div class="row gks_td0875">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες της επαφής');?></div>
            
            <form role="form" method="post" action="admin-users-item-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="user_id" id="user_id" value="<?php echo $id;?>">
              <div id="lightgallery_user">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_users_photo where user_id=".$id." and filesobjectlist=0 order by id_user_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_user_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_user_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('wp_users',$id);?>
            </form>                      
            
            
          </div>

        </div>
      </div>        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σχόλια - Παρατηρήσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('notes');?>> 
                        
            <div class="form-group row">
              <label for="pelati_sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο για πελάτη');?>:</label>
              <div class="col-md-8">
                <textarea id="pelati_sxolio" type="text" class="form-control form-control-sm myneedsave" style="height:80px;"><?php echo htmlspecialchars_gks($row['pelati_sxolio']);?></textarea>
              </div>
            </div>    
            <div class="form-group row">
              <label for="order_sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο για παραγγελία');?>:</label>
              <div class="col-md-8">
                <textarea id="order_sxolio" type="text" class="form-control form-control-sm myneedsave" style="height:80px;"><?php echo htmlspecialchars_gks($row['order_sxolio']);?></textarea>
              </div>
            </div>         
        </div>
      </div>        
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φορολογικά και Ασφαλιστικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('foro');?>>        

          <div class="form-group row">
            <label for="arithmos_tautoitas" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Ταυτότητας');?>:</label>
            <div class="col-md-8">
              <input id="arithmos_tautoitas" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['arithmos_tautoitas']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="arxi_ekdosis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αρχή Έκδοσης');?>:</label>
            <div class="col-md-8">
              <input id="arxi_ekdosis" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['arxi_ekdosis']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="amka" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΜΚΑ');?>:</label>
            <div class="col-md-8">
              <input id="amka" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['amka']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ama_eam" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΜΑ - ΕΑΜ');?>:</label>
            <div class="col-md-8">
              <input id="ama_eam" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ama_eam']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <select id="fiscal_position_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                where fiscal_position_disable=0 
                order by fiscal_position_sortorder,fiscal_position_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <select id="pricelist_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="generic_ekprosi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γενική Έκπτωση');?>:</label>
            <div class="col-md-8">
              <input id="generic_ekprosi" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['generic_ekprosi'],true);?>" min="0" step="0.01" style="width:100px;display: inline-block;text-align:right" autocomplete="<?php echo $autocomplete_gks_disable;?>"> %
              <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί το παραπάνω ποσοστό εφόσον δεν υπάρχει άλλη έκπτωση από τον τιμοκατάλογο');?></small>
            </div>
          </div>                   
          
          <div class="form-group row">
            <label for="eponimia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-md-8">
              <input id="eponimia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eponimia']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['title']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="afm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-md-8">
              <input id="afm" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['afm']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="doy" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
            <div class="col-md-8">
              <input id="doy" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['doy']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="epaggelma" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
            <div class="col-md-8">
              <input id="epaggelma" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['epaggelma']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="gemi_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γ.Ε.ΜΗ.');?>:</label>
            <div class="col-md-8">
              <input id="gemi_number" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['gemi_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted"><a href="https://publicity.businessportal.gr/" target="_blank"><?php echo gks_lang('Αναζήτηση');?></a></small>
            </div>
          </div>
                     
          <div class="form-group row">
            <label for="is_b2g" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('B2G');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_b2g" value="1" <?php if ($row['is_b2g']!=0) echo ' checked '; ?> class="switchery1_sel">
              <small class="form-text text-muted"><a href="https://webapps.gsis.gr/dsae2/foreisreg/faces/pages/mainmenu/entrance.xhtml" target="_blank"><?php echo gks_lang('Αναζήτηση');?></a></small>
            </div>
          </div>           
          <div id="div_is_b2g" style="<?php if ($row['is_b2g']=='0') echo 'display:none;';?>">
            <div class="form-group row">
              <label for="b2g_aaht_code" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Κωδικός ΑΑΗΤ');?>:</label>
              <div class="col-md-8">
                <input id="b2g_aaht_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_aaht_code']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="b2g_aaht_name" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ονομασία ΑΑΗΤ');?>:</label>
              <div class="col-md-8">
                <input id="b2g_aaht_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_aaht_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="b2g_aaht_foreas" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Φορέας');?>:</label>
              <div class="col-md-8">
                <input id="b2g_aaht_foreas" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_aaht_foreas']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="b2g_aaht_typos_forea" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Τύπος Φορέα');?>:</label>
              <div class="col-md-8">
                <input id="b2g_aaht_typos_forea" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_aaht_typos_forea']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="b2g_aaht_kodikos_ekatharisis" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Κωδικός Υπηρεσίας Εκκαθάρισης');?>:</label>
              <div class="col-md-8">
                <input id="b2g_aaht_kodikos_ekatharisis" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_aaht_kodikos_ekatharisis']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>



            
            
            
          </div>              
          
        </div>
      </div>        
        










        
      <div class="card gks_card_expand" id="user_roles_div">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ρόλοι Επαφής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('roles');?>> 

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ρόλοι');?>:</label>
            <div class="col-md-8 gks_td0875">
              
              <?php
              $gks_wp_system_roles = gks_wp_system_roles_func();
              $min_hierarchy_login_user=9999;
              foreach ($my_wp_user_info->roles as $value) {
                if (isset($gks_wp_system_roles[$value]) and $gks_wp_system_roles[$value]['hierarchy'] < $min_hierarchy_login_user) 
                  $min_hierarchy_login_user = $gks_wp_system_roles[$value]['hierarchy'];
              }
              if ($min_hierarchy_login_user==1) $min_hierarchy_login_user=0; //ean einai admin, na mporei na kanei admin
              
              
              //print '<pre>';
              //print_r(GKS_ROLES_HIERARCHY);
              //print_r($gks_wp_system_roles);
              //print_r($user_roles);
              //print '</pre>';
              foreach ($gks_wp_system_roles as $role_item) {
                if ($role_item['hierarchy'] > $min_hierarchy_login_user) {
                  $role_checked='';
                  if (in_array($role_item['id'],$user_roles)) $role_checked='checked';
                  echo '<input class="rolecheckbox" type="checkbox" style="height:32px" name="role_'.$role_item['id'].'" id="role_'.$role_item['id'].'" value="'.$role_item['id'].'" '.
                  $role_checked. '> '.$role_item['name'].'<br>';
                }
              } 
              
              
              ?>
              
              
              
              <?php if ($show_admin) { ?>               
               
              <?php } 
              
              $my_disabled ='';
              if ($show_others) {
              	$my_disabled =' disabled ';	
              }	
              ?>
                

              
            </div>
          </div>
          
          <?php if (ur_ad()) {?>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δικαιώματα');?>:</label>
            <div class="col-md-8">          
              <a href="admin-users-item-permission.php?id=<?php echo $id;?>" class="btn btn-primary"><?php echo gks_lang('Επεξεργασία');?></a>
            </div>
          </div>
          <?php } ?>

        </div>
      </div>



   

      
      
    </div>    
  </div>    
</div> 

<div class="container-fluid ">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βιογραφικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bio');?>> 
          <div class="row">
            <div class="col-md-6">
              
              <?php if ($show_admin or $show_hr or $show_lo ) { ?>        
              
              <div class="form-group row">
                <label for="onoma_patera" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πατρώνυμο');?>:</label>
                <div class="col-md-8">
                  <input id="onoma_patera" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['onoma_patera']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>        
              <div class="form-group row">
                <label for="onoma_miteras" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μητρώνυμο');?>:</label>
                <div class="col-md-8">
                  <input id="onoma_miteras" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['onoma_miteras']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>        
              <div class="form-group row">
                <label for="gks_sex" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φύλο');?>:</label>
                <div class="col-md-8">
                  <select id="gks_sex" class="form-control form-control-sm myneedsave">
                    <option value="0"></option>
                    <option value="1" <?php if ($row['gks_sex']==1) echo " selected ";?>><?php echo gks_lang('Άρρεν');?></option>
                    <option value="2" <?php if ($row['gks_sex']==2) echo " selected ";?>><?php echo gks_lang('Θύλη');?></option>
                  </select>    
                </div>
              </div> 
              <div class="form-group row">
                <label for="oikogeniaki_katastasti_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οικογενειακή Κατάσταση');?>:</label>
                <div class="col-md-8">
                  <select id="oikogeniaki_katastasti_id" class="form-control form-control-sm myneedsave">
                    <option value="0"></option>
                    <?php
                    $sql="select * FROM gks_users_oikogeniaki_katastasti ORDER BY oikogeniaki_katastasti_sortorder ";
                    $result_select = $db_link->query($sql);        
                    if (!$result_select) {
                      debug_mail(false,'admin-users-item.php error sql',$sql);
                      die('sql error');
                    }
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_oikogeniaki_katastasti'].'" ';
                      if ($row_select['id_oikogeniaki_katastasti']==$row['oikogeniaki_katastasti_id']) echo ' selected ';
                      echo '>'.gks_lang($row_select['oikogeniaki_katastasti_descr'].'','part4','oikogeniaki_katastasti_descr').'</option>';
                    }?>
                  </select>    
                </div>
              </div> 
                      
              <div class="form-group row">
                <label for="oikogeniaki_katastasti_paidia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παιδιά');?>:</label>
                <div class="col-md-8">
                  <input id="oikogeniaki_katastasti_paidia" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['oikogeniaki_katastasti_paidia']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
              
              <?php } ?>               
              <?php if ($show_admin or $show_hr or $show_lo) { ?>   
              <div class="form-group row">
                <label for="genisi_date" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Γέννησης');?>:</label>
                <div class="col-md-8">
                  <input id="genisi_date" type="text" class="form-control form-control-sm " value="<?php 
                  if (isset($row['genisi_date']) and $row['genisi_date'] !='') { echo date('d/m/Y', strtotime($row['genisi_date']));}
                  ?>" autocomplete="<?php echo $autocomplete_gks_disable;?>"> <span id="span_calc_age" style="font-style: italic;" class="gks_td0875"></span>
                </div>
              </div> 
              <?php } ?>
              <?php if ($show_admin or $show_hr) { ?>    
              <div class="form-group row">
                <label for="cv_mitriki_glossa" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μητρική Γλώσσα');?>:</label>
                <div class="col-md-8">
                  <input id="cv_mitriki_glossa" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cv_mitriki_glossa']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="cv_jenes_glosses" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ξένες Γλώσσες');?>:</label>
                <div class="col-md-8">
                  <input id="cv_jenes_glosses" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cv_jenes_glosses']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="cv_sxesi_me_photografia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχέση με την Φωτογραφία');?>:</label>
                <div class="col-md-8">
                  <input id="cv_sxesi_me_photografia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cv_sxesi_me_photografia']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
                
                
              <div class="form-group row">
                <label for="cv_metaforiko_meso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μεταφορικό Μέσο');?>:</label>
                <div class="col-md-8">
                  <input class="myneedsave" type="radio" name="cv_has_moto" id="cv_has_null" <?php if ($row['cv_has_bike'] ==0 && $row['cv_has_motorcycle'] ==0 && $row['cv_has_car'] ==0) echo 'checked ';?>> <label for="cv_has_null" class="gks_td0875"><?php echo gks_lang('Τίποτα');?></label></label><br>
                  <input class="myneedsave" type="radio" name="cv_has_moto" id="cv_has_bike" <?php if ($row['cv_has_bike'] !=0) echo 'checked ';?>> <label for="cv_has_bike" class="gks_td0875"><?php echo gks_lang('Έχει ποδήλατο');?></label><br>
                  <input class="myneedsave" type="radio" name="cv_has_moto" id="cv_has_motorcycle" <?php if ($row['cv_has_motorcycle'] !=0) echo 'checked ';?>> <label for="cv_has_motorcycle" class="gks_td0875"><?php echo gks_lang('Έχει μηχανή');?></label><br>
                  <input class="myneedsave" type="radio" name="cv_has_moto" id="cv_has_car" <?php if ($row['cv_has_car'] !=0) echo 'checked ';?>> <label for="cv_has_car" class="gks_td0875"><?php echo gks_lang('Έχει αυτοκίνητο');?></label><br>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="gks_td0875"><?php echo gks_lang('Θέσεις');?>:</span> <select name="cv_has_car_theseis" id="cv_has_car_theseis" class="gks_td0875 myneedsave">
                        <?php 
                        $temphas= $row['cv_has_car_theseis'];
                        if ($temphas==0) $temphas=5;
                        ?>
                        <option <?php if ($temphas == 1) echo"selected";?>>1</option>
                        <option <?php if ($temphas == 2) echo"selected";?>>2</option>
                        <option <?php if ($temphas == 3) echo"selected";?>>3</option>
                        <option <?php if ($temphas == 4) echo"selected";?>>4</option>
                        <option <?php if ($temphas == 5) echo"selected";?>>5</option>
                        <option <?php if ($temphas == 6) echo"selected";?>>6</option>
                        <option <?php if ($temphas == 7) echo"selected";?>>7</option>
                        <option <?php if ($temphas == 8) echo"selected";?>>8</option>
                        <option <?php if ($temphas == 9) echo"selected";?>>9</option>
                        <option <?php if ($temphas == 10) echo"selected";?>>10</option>
                      </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="cv_metaforiko_meso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Άλλο μεταφορικό Μέσο');?>:</label>
                <div class="col-md-8">
                  <input id="cv_metaforiko_meso" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cv_metaforiko_meso']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
                                                                           
              <div class="form-group row">
                <label for="ethnikotita" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εθνικότητα');?>:</label>
                <div class="col-md-8">
                  <input id="ethnikotita" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ethnikotita']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>                  
              <?php } ?>
                    
            </div>
            <div class="col-md-6">
              
              <?php if ($show_admin or $show_hr) { ?>         
              <div class="form-group row">
                <label for="description" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σύντομο βιογραφικό');?>:</label>
                <div class="col-md-8">
                  <textarea id="description" type="text" class="form-control form-control-sm" style="height:80px;"><?php if ($id>0) echo get_user_meta($id, 'description', true);?></textarea>
                </div>
              </div>
              


              <div class="form-group row">
                <label for="alli_apasxolisi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Άλλη Απασχόληση');?>:</label>
                <div class="col-md-8">
                  <input id="alli_apasxolisi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['alli_apasxolisi']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="cv_proipiresia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προϋπηρεσία');?>:</label>
                <div class="col-md-8">
                  <textarea id="cv_proipiresia" type="text" class="form-control form-control-sm myneedsave" style="height:80px;"><?php echo htmlspecialchars_gks($row['cv_proipiresia']);?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <label for="cv_spoydes" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σπουδές');?>:</label>
                <div class="col-md-8">
                  <textarea id="cv_spoydes" type="text" class="form-control form-control-sm myneedsave" style="height:80px;"><?php echo htmlspecialchars_gks($row['cv_spoydes']);?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <label for="cv_seminaria" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σεμινάρια');?>:</label>
                <div class="col-md-8">
                  <textarea id="cv_seminaria" type="text" class="form-control form-control-sm myneedsave" style="height:80px;"><?php echo htmlspecialchars_gks($row['cv_seminaria']);?></textarea>
                </div>
              </div>                  
              <?php } ?>
              
              <?php if ($show_admin or $show_hr) { ?>         
              <div class="form-group row">
                <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνημμένα αρχεία<br>π.χ. πλήρες βιογραφικό,<br>συστάσεις,<br>πτυχία,<br>πιστοποιητικά κτλ');?>:</label>
                <div class="col-md-8">
                <form role="form" method="post" action="profile-cv-upload.php?user_id=<?php echo $id;?>&show_on_user_profile=0" id="mycv_upload" enctype="multipart/form-data">
                  <div class="form-group" id="imagelist_cv">                        
                  <?php   
                  $sql="select * from gks_users_cv where user_id=".$id." order by id_user_cv";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<span id="item_upload_cv_' . $row_select['id_user_cv'] . '">
                    <input type="checkbox" class="input_item_upload_cv_check"
                    name="input_item_upload_cv_check_' . $row_select['id_user_cv'] . '" 
                    id="input_item_upload_cv_check_' . $row_select['id_user_cv'] . '" 
                    '.($row_select['show_on_user_profile']!=0 ? ' checked ': '').'
                    title="'.gks_lang('Ορατό στο προφίλ του χρήστη').'"
                    > <input type="text" class="input_item_upload_cv"
                    name="input_item_upload_cv_' . $row_select['id_user_cv'] . '" 
                    id="input_item_upload_cv_' . $row_select['id_user_cv'] . '" 
                    value="'.$row_select['file_descr'].'"
                    placeholder="'.gks_lang('Περιγραφή π.χ. Βιογραφικό').'"
                    title="'.gks_lang('Περιγραφή του αρχείου π.χ. Βιογραφικό, συστατική επιστολή, πτυχίο').'"
                    style="width: 180px;max-width: 100%;margin-top: 4px;"
                    > <a href="'.$row_select['cv_url'].'" target="_blank">'.mb_basename($row_select['cv_url']).' ('.number_format($row_select['mysize']/1024/1024,2,',','.').' MB)</a>
                    <a href="" class="delete_upload_cv" data-id="'.$row_select['id_user_cv'].'"><img src="/my/img/0.png" border="0" width="16" style="position: relative;top: 3px;"></a><br></span>';
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
              <?php } ?>       
                              
              <div class="form-group row">
                <label for="sistasi_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σύσταση από');?>:</label>
                <div class="col-md-8">
                  <input id="sistasi_from" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sistasi_from']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>  
              <div class="form-group row">
                <label for="sistasi_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιθυμητές ημέρες εργασίας');?>:</label>
                <div class="col-md-8">
                <?php
                 
                $days_to_work=array();
                if (isset($row['days_to_work']) and $row['days_to_work']!='') {
                  $days_to_work = unserialize($row['days_to_work']);
                }?>    
                <input class="myneedsave" type="checkbox" name="days_to_work1" id="days_to_work1" <?php if (isset($days_to_work[1]) and $days_to_work[1]!=0) echo 'checked ';?>> <label for="days_to_work1" class="gks_td0875"><?php echo getWeekDayName(1);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work2" id="days_to_work2" <?php if (isset($days_to_work[2]) and $days_to_work[2]!=0) echo 'checked ';?>> <label for="days_to_work2" class="gks_td0875"><?php echo getWeekDayName(2);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work3" id="days_to_work3" <?php if (isset($days_to_work[3]) and $days_to_work[3]!=0) echo 'checked ';?>> <label for="days_to_work3" class="gks_td0875"><?php echo getWeekDayName(3);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work4" id="days_to_work4" <?php if (isset($days_to_work[4]) and $days_to_work[4]!=0) echo 'checked ';?>> <label for="days_to_work4" class="gks_td0875"><?php echo getWeekDayName(4);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work5" id="days_to_work5" <?php if (isset($days_to_work[5]) and $days_to_work[5]!=0) echo 'checked ';?>> <label for="days_to_work5" class="gks_td0875"><?php echo getWeekDayName(5);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work6" id="days_to_work6" <?php if (isset($days_to_work[6]) and $days_to_work[6]!=0) echo 'checked ';?>> <label for="days_to_work6" class="gks_td0875"><?php echo getWeekDayName(6);?></label><br>
                <input class="myneedsave" type="checkbox" name="days_to_work0" id="days_to_work0" <?php if (isset($days_to_work[0]) and $days_to_work[0]!=0) echo 'checked ';?>> <label for="days_to_work0" class="gks_td0875"><?php echo getWeekDayName(0);?></label><br>
                </div>
              </div>                       
                      
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>  
</div>  

<div class="container-fluid ">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τραπεζικοί λογαριασμοί');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('banks');?>>      

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τραπεζικοί λογαριασμοί');?>:</label>
            <div class="col-md-8 gks_td0875">
              <?php
              $sql_bank_accounts="SELECT gks_bank_accounts.*, gks_banks.bank_descr
              FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
              WHERE gks_bank_accounts.user_id=".$id."
              ORDER BY gks_bank_accounts.id_bank_account";
              $result_bank_accounts = $db_link->query($sql_bank_accounts);        
              if (!$result_bank_accounts) {
                debug_mail(false,'error sql',$sql_bank_accounts);
                die('sql error');
              }
              $i = 0;
              while ($row_bank_accounts = $result_bank_accounts->fetch_assoc()) { 
                $i++;
                
                  echo '<a href="admin-bank_accounts-item.php?id='.$row_bank_accounts['id_bank_account'].'">
                  <i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
          
                  echo 'IBAN: ';
                  
                  $iban = iban_to_machine_format($row_bank_accounts['IBAN']);
                  
                  if(verify_iban($iban)) {
                    echo iban_to_human_format($iban);
                  } else {
                    echo $row_bank_accounts['IBAN'];
                  }
                  
                  echo '<br>'.
                  gks_lang('Τράπεζα').': '.$row_bank_accounts['bank_descr'].'<br>'.
                  gks_lang('Δικαιούχος').': '.$row_bank_accounts['account_dikaiouxos'];
                  
                  echo '<hr>';
               }
               
                echo '<a href="admin-bank_accounts-item.php?id=-1&user_id='.$id.'">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size:150%;position:relative;top:3px;"></i>
                '.gks_lang('Προσθήκη').'</a>';
               ?>
            </div>
          </div>      


        </div>
      </div>




    </div>
    
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Newsletter');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('news');?>> 

          <?php
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
          //print_r($nl_emails);
          
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
          //print_r($nl_sms);      
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
  
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo $row_newsletter_lists['newsletter_list_title'];?></label>
            <div class="col-md-4">
              <input type="checkbox" name="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" id="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" class="newsletter-email" data-id="<?php echo $row_newsletter_lists['id_newsletter_list'];?>"
              <?php if (isset($nl_emails[$row_newsletter_lists['id_newsletter_list']]) and $nl_emails[$row_newsletter_lists['id_newsletter_list']] == 1) {
                echo " checked ";;
              } ?>
              >
              <label for="newsletter-email-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" style="display:inline" class="gks_td0875">email</label>
            </div>
            <div class="col-md-4">
              <input type="checkbox" name="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" id="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" class="newsletter-sms" data-id="<?php echo $row_newsletter_lists['id_newsletter_list'];?>"
              <?php if (isset($nl_sms[$row_newsletter_lists['id_newsletter_list']]) and $nl_sms[$row_newsletter_lists['id_newsletter_list']] == 1) {
                echo " checked ";;
              } ?>
              >
              <label for="newsletter-sms-<?php echo $row_newsletter_lists['id_newsletter_list'];?>" style="display:inline" class="gks_td0875">SMS</label>    
            </div>
          </div>

          <?php } ?> 
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ποσοστό συμπλήρωσης του προφίλ');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('posost');?>> 

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ως χρήστης');?>:</label>
            <div class="col-md-8">
              <div class="progress" style="height: 26px;margin-bottom: 24px;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $profilepososto_user;?>%;" aria-valuenow="<?php echo $profilepososto_user;?>" aria-valuemin="0" aria-valuemax="100"><?php echo $profilepososto_user;?>%</div>
              </div>
            </div>
          </div>
          
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ως συνεργάτης');?>:</label>
            <div class="col-md-8">
              <div class="progress" style="height: 26px;margin-bottom: 24px;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $profilepososto_job;?>%;" aria-valuenow="<?php echo $profilepososto_job;?>" aria-valuemin="0" aria-valuemax="100"><?php echo $profilepososto_job;?>%</div>
              </div>
            </div>
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
<?php if ($show_admin or $show_hr or $show_lo) { ?>  
      <button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" style="margin-bottom:10px;" class="btn btn-danger deleterowbtn" data-id="<?php if ($id>0) echo $id;?>" data-model="wp_users" data-backurl="admin-users.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
<?php } ?>      
      
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<?php if ($id>0) {?>      
<div class="container-fluid">
  <div class="form-group row">
    <div class="col-md-12 text-center mt-2">
      <br><?php echo gks_lang('Τελευταία ενημέρωση από τον χρήστη');?>: <?php if (isset($row['gks_last_update'])) echo showDate(strtotime($row['gks_last_update']), 'd/m/Y H:i:s', 1); else echo gks_lang('Ποτέ');?>
    </div> 
  </div> 
</div> 
<?php } ?>



    

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
        
      <?php if ($show_admin or $show_hr or $show_lo or $show_others) { ?>

                    
      <?php 
      echo getObjectRels('wp_users',$id);
      echo getActivityObjectTable('wp_users',$id);
      ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_users_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_users_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_users_messages.userfor_id=".$id."
            ORDER BY gks_users_messages.mydate_add DESC, gks_users_messages.id_users_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_users_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php 
                if (!empty($row_msg['woo_author'])) echo $row_msg['woo_author'];
                else echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['userfor_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>      
      
      <?php 
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'wp_users','id'=>$id));
      echo $obj_fileslist['html'];      
      ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ομάδες επαφών');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('omades');?>>        

          <?php
            
          $sql = "SELECT gks_users_groups_users.*, gks_users_groups.group_title,gks_users_groups.group_disable,
          CONCAT_WS('\\\\',
                          ug10.group_title,
                          ug9.group_title,
                          ug8.group_title,
                          ug7.group_title,
                          ug6.group_title,
                          ug5.group_title,
                          ug4.group_title,
                          ug3.group_title,
                          ug2.group_title,
                          gks_users_groups.group_title) as group_descr
          FROM (((((((((gks_users_groups_users 
          LEFT JOIN gks_users_groups ON gks_users_groups_users.group_id = gks_users_groups.id_users_group)
          LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
          LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
          LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
          LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
          LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
          LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
          LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
          LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
          LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group
          WHERE gks_users_groups_users.user_id=".$id." 
          ORDER BY group_descr;";
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
              <tr >	
                  <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
          <?php if ($show_admin or $show_hr or $show_lo) { ?>                
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ></th> 
          <?php } ?>
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Ομάδα');?></th> 
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Ενεργή');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ομαδάρχης');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        
              </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_users_groups_users'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>      
            <?php if ($show_admin or $show_hr or $show_lo) { ?>  
              <td nowrap align="center"><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_users_groups_users'];?>" data-model="gks_users_groups_users"></i></td>
            <?php } ?>          
              <td nowrap><?php echo '<a href="admin-usersgroups-item.php?id='.$row_list['group_id'].'">'.$row_list['group_descr'].'</a>';;?></td>  
              
              <td class="mytdcm"><?php echo myimg010r($row_list['group_disable']);?></td>
              <td nowrap align="center"><img src="img/<?php echo $row_list['is_omadarxis']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td> 
              <td nowrap><?php if (isset($row_list['add_date'])) echo showDate(strtotime($row_list['add_date']), 'd/m/Y H:i:s', 1);?></td>  
            </tr>
          <?php } ?>
                
          <?php if ($show_admin or $show_hr or $show_lo) { ?>  
            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="group"    id="group"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="group_id" id="group_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center">
                
              </td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_group" <?php if ($id<=0) echo 'disabled';?>>
                <?php echo gks_lang('Προσθήκη');?>
                </button>
              </td>  
            </tr>      
                
          <?php } ?>
                
          </tbody>
          </table>


        
        </div>         
      </div>
      <?php } ?>
        


        
      <?php if (ur_ad() or ur_hr() or ur_lo()) { ?>   
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεσμοι');?>
        </div>
        <div class="card-body gks_td0875" <?php echo gks_card_body('links');?>>        
          
          <p align="center"><a href="admin-users-item-export-logistis.php?id=<?php echo $id?>" ><?php echo gks_lang('Εξαγωγή για λογιστή');?> <img src="img/word.png" width="16" border="0"></a></p>
          <?php if (ur_ad() or ur_lo()) { ?> 
          <p align="center"><?php echo gks_lang('Υπόλοιπο Προκαταβολής');?>: <?php echo myCurrencyFormat(get_user_ypoloipo_prokatavolis($id),true,true);?> <a href="admin-bank-transactions-ypoloipa.php?user_id=<?php echo $id;?>&expand=1"><?php echo gks_lang('Λεπτομέρειες');?></a></p>
          <p align="center"><a href="admin-bank-accounts.php?fdate_edit=1&fuser=<?php echo $id?>&fbank=-1&fshoweshop=-1&fdeletedfromuser=-1&search_string=" ><?php echo gks_lang('Τραπεζικοί Λογαριασμοί');?></a></p>
          <p align="center"><a href="admin-bank-transactions.php?fbtrastate=-1&fmydate=1&ffor_month=-1&ffor_year=-1&fcompany=-1&fuser=<?php echo $id?>&fbank_id_from=-1&fbank_id_to=-1&fmydate_exec=1&fedituser=-1&fimport_file=-1&fninp=-1&search_string=" ><?php echo gks_lang('Τραπεζικές συναλλαγές');?></a></p>
          <p align="center"><a href="/wp-admin/admin.php?page=WFLS&user=<?php echo $id?>">2FA</a></p>

          
          <?php } ?>                  
        
        </div>         
      </div>
            
      <?php } ?>
      
    </div>
    <div class="col-md-6">

      <?php 
      $protypdays_array =array();
      if ($show_admin or $show_hr or $show_lo) { ?>        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εταιρείες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('company');?>> 
          <?php
          $sql = "SELECT gks_company_users.*, gks_company.company_title,gks_company.company_disable
          FROM gks_company_users LEFT JOIN gks_company ON gks_company_users.company_id = gks_company.id_company
          WHERE (((gks_company_users.user_id)=".$id."))
          ORDER BY gks_company.company_sortorder, gks_company.company_title;";
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
              <tr >	
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" >#</th>
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="30%"><?php echo gks_lang('Εταιρεία');?></th> 
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Ενεργή');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('Πρόσληψη');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('Ημερομηνία');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"><?php echo gks_lang('Σχόλιο');?></th>        
              </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_company_users'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>      
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_company_users'];?>" data-model="gks_company_users"></i>
              </td>
              <td nowrap><?php echo '<a href="admin-company-item.php?id='.$row_list['company_id'].'">'.$row_list['company_title'].'</a>';;?></td>  
              <td class="mytdcm"><?php echo myimg010r($row_list['company_disable']);?></td>
              <td nowrap><?php echo date('d/m/Y',strtotime($row_list['date_hire']));?></td>   
              <td nowrap><?php echo showDate(strtotime($row_list['add_date']), 'd/m/Y H:i:s', 1);?></td>  
              <td nowrap class="edit_company_sxolio" data-id="<?php echo $row_list['id_company_users']?>"><?php echo nl2br_gks($row_list['sxolio']);?></td>  
            </tr>
                  
          <?php } ?>  
            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>  
              <td nowrap align="center">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
                
              </td>
              <td nowrap colspan="2">
                <input type="text"   name="company_user"    id="company_user"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="company_user_id" id="company_user_id">
              </td>  
              <td nowrap align="center">
                <input type="text"   name="date_hire"    id="date_hire"   class="form-control" style="width:98%;min-width:50px" >
              </td>
              <td nowrap>
              </td>                    
              <td nowrap><textarea name="companyusersxolio"    id="companyusersxolio" class="form-control" style="width:98%;min-width:50px:height:60px"></textarea> 
              </td>                    
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center">
                
              </td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_company_user" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>                     
          </tbody>
          </table>
          
          
          <?php 
          if (ur_ad() or ur_lo()) { ?> 
          <div id="protypdays_error" align="center" style="display:none; height1:50px;margin-top:10px">
            <div id="protypdays_errorsub" style="background-color:red;color:white;width:100%;padding:20px;">
              <b><?php echo gks_lang('Προσοχή, υπάρχει κάποιο πρόβλημα');?></b>
            </div>
          </div>
          <div style="padding-top:16px">
            <p align="center" class="gks_td0875"><?php echo gks_lang('Πρότυπες Ημέρες Ασφάλισης');?>:<br><span id="span_protypdays_descr"><?php 
            echo get_user_protypdays_descr($id,$protypdays_array,'<br>');
            //print_r($protypdays_array);
            ?></span><br><span id="span_protypdays_change" style="cursor:pointer;color:blue;text-decoration: underline;"><?php echo gks_lang('Αλλαγή');?></span>
            </p>
          </div>
          <?php } ?>

        </div>
      </div>
      <?php } ?>       
      
    </div>
  </div>
</div>


<?php if ($show_admin or $show_hr or $show_lo) { ?> 

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      



      <div class="card gks_card_expand" id="gks_card_extra_address">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Άλλες διευθύνσεις της επαφής');?></span>
          <?php if ($id>0) {?>
          <a class="btn btn-sm btn-primary gks_stoppropagation" style="margin-left:10px;" href="admin-users-extra_address-item.php?id=-1&user_id=<?php echo $id;?>">
            <?php echo gks_lang('Προσθήκη');?>
          </a>
          <?php } ?>
        </div>
        <div class="card-body" <?php echo gks_card_body('addroth');?>> 

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
              <tr >	
                  <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo gks_lang('Όνομα');?></th> 
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo gks_lang('Τηλέφωνο');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Οδός');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo gks_lang('Όροφος');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" ><?php echo gks_lang('Περιοχή');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Πόλη');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo gks_lang('ΤΚ');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Νομός');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" ><?php echo gks_lang('Χώρα');?></th>        
              </tr>
          </thead>
          <tbody>
            
          <?php
$lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
$lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));

          
            $sql="SELECT gks_users_extra_address.*, 
            ".gks_lang_sql_field('country_name',$lang_prepare_gks_country).",
            ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)."
            FROM 
            ".$lang_prepare_gks_country['sql']['from1']."
            ".$lang_prepare_gks_nomos['sql']['from1']."
            ((gks_users_extra_address 
            LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
            LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos)
            ".$lang_prepare_gks_country['sql']['from2']."
            ".$lang_prepare_gks_nomos['sql']['from2']."
            
            WHERE gks_users_extra_address.user_id=".$id."
            ORDER BY gks_users_extra_address.id_users_extra_address";  
            //echo $sql;die(); 
            $result_ea = $db_link->query($sql); 
            if (!$result_ea) {
              debug_mail(false,'admin-users-item.php error sql',$sql);
              die('sql error');
            }
            $i = 0;
            while ($row_ea = $result_ea->fetch_assoc()) {
          	  $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap align="right"><?php echo $i;?></td>      

              <td nowrap class="mytdcm p-0">
                <table class="tableids3col">
                  <tr>
                    <td><a href="admin-users-extra_address-item.php?id=<?php echo $row_ea['id_users_extra_address'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
                    <td><?php echo $row_ea['id_users_extra_address'];?></td>
                    <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row_ea['id_users_extra_address'];?>" data-model="gks_users_extra_address"></i></td>
                  </tr>      
                </table>
              </td>

              
              <td        align="left"><?php echo $row_ea['ea_name'];?></td>      
              <td        align="left"><?php echo $row_ea['ea_phone'];?></td>      
              <td        align="left"><?php echo $row_ea['ea_odos'].' '.$row_ea['ea_arithmos'];?></td>      
              <td nowrap align="left"><?php echo $row_ea['ea_orofos'];?></td>      
              <td        align="left"><?php echo $row_ea['ea_perioxi'];?></td>      
              <td        align="left"><?php echo $row_ea['ea_poli'];?></td>      
              <td nowrap align="left"><?php echo $row_ea['ea_tk'];?></td>      
              <td        align="left"><?php echo $row_ea['nomos_descr'];?></td>      
              <td        align="left"><?php echo $row_ea['country_name'];?></td>      
            </tr>
            
          <?php } ?>  
          </tbody>                  
          </table>              
        </div>
      </div>                  

    </div>
  </div>
</div>
<?php } ?> 






<?php if ($show_admin or $show_hr or $show_lo) { 
  
?>





<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφές SMS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logsms');?>> 

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'   >#</th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Ημερομηνία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Επαφή');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Από');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Προς');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" ><?php echo gks_lang('Μήνυμα');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Κομμάτια');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Κόστος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" data-toggle="tooltip" data-html="true" title="Αποτέλεσμα αποστολής"><?php echo gks_lang('Αποτ.');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Κατάσταση');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" data-toggle="tooltip" data-html="true" title="Ημερομηνία αναφοράς παράδοσης"><?php echo gks_lang('Ημερομηνία ΑΠ');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $sql = "SELECT gks_sms.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_sms 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_sms.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          where (model='user_add' and model_id=".$id.")";
          if (isset($user_mobile) and $user_mobile!='') {
            $sql.=" or gks_sms.myto like '%".$user_mobile ."%'";
          }
          $sql.= " ORDER BY date_add desc limit 10";
          
          $result = $db_link->query($sql); 
          if (!$result) debug_mail(false,'error sql',$sql);
          if (!$result) die('sql error');
          
          
          $i = 0;
          while ($row_sms = $result->fetch_assoc()) {
            $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>      
                
              <td nowrap><?php echo showDate(strtotime($row_sms['date_add']), 'd/m/Y H:i:s', 1);?></td>   
              <td nowrap><?php echo $row_sms['gks_nickname'];?></td>  
              <td nowrap><?php echo $row_sms['myfrom'];?></td>      
              <td nowrap><?php echo $row_sms['myto'];?></td>      
              <td       ><?php echo $row_sms['Message'];?></td>      
              <td nowrap><?php echo $row_sms['Parts'];?></td>      
              <td nowrap align="right"><?php echo $row_sms['points'];?></td>      
              <td nowrap style="text-align: center !important;"><img src="img/<?php echo $row_sms['myret'];?>.png" border="0" width="16"></td>      
              <td nowrap style="text-align: center !important;"><span class="sms_status sms_status_<?php echo $row_sms['status'];?>"><?php echo $row_sms['status_name'];?></span> </td>
              <td nowrap><?php if (isset($row_sms['donedate_date'])) echo showDate(strtotime($row_sms['donedate_date']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>
          </tbody>
          </table>

          <div class="form-group row" style="padding-top: 24px;">
            <div class="col-md-12" style="text-align:center">
              <a class="btn btn-sm btn-primary" href="admin-log-sms.php?fdate=1&user=-1&myfrom=-1&parts=-1&myret=-1&status_name=-1&fdonedate=1&model=-1&search_string=<?php echo urlencode($user_mobile) ;?>" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Όλες οι εγγραφές ...');?></a>
            </div>
          </div>   

        </div>
      </div>                  

    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφές emails');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logemail');?>> 

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'   >#</th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Ημερομηνία');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Επαφή');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Από');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Από Όνομα');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" ><?php echo gks_lang('Απάντηση σε');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Αποστολέας');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Προς');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"   ><?php echo gks_lang('Προς Όνομα');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   ><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" ><?php echo gks_lang('Θέμα');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" data-toggle="tooltip" data-html="true" title="<?php echo gks_lang('Αποτέλεσμα αποστολής');?>"><?php echo gks_lang('Αποτ.');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $sql = "SELECT gks_email.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_email 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_email.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          where (model in ('user_add','profile') and model_id=".$id.")";
          if (isset($user_email) and $user_email!='') {
            $sql.=" or gks_email.myto like '".$db_link->escape_string($user_email)."'";
          }
          $sql.=" ORDER BY date_add desc limit 10";
          $result = $db_link->query($sql); 
          if (!$result) debug_mail(false,'error sql',$sql);
          if (!$result) die('sql error');
          
          
          $i = 0;
          while ($row_email = $result->fetch_assoc()) {
            $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row nowrap align="right"><?php echo ($i);?></td>      
                
              <td nowrap><?php echo showDate(strtotime($row_email['date_add']), 'd/m/Y H:i:s', 1);?></td>   
              <td nowrap><?php echo $row_email['gks_nickname'];?></td>      
              <td nowrap><?php echo $row_email['myfrom'];?></td>      
              <td nowrap><?php echo $row_email['myfrom_name'];?></td>      
              <td nowrap><?php echo $row_email['replyto'];?></td>      
              <td nowrap><?php echo $row_email['sender'];?></td>      
              <td nowrap><?php echo $row_email['myto'];?></td>      
              <td nowrap><?php echo $row_email['myto_name'];?></td>      
              <td nowrap align="center">
                <i class="fas fa-envelope gks_email_view" data-id="<?php echo $row_email['id'];?>"></i>
              </td>      
              <td       ><?php echo $row_email['subject'];?></td>      
              <td nowrap style="text-align: center !important;"><img src="img/<?php echo $row_email['myret'];?>.png" border="0" width="16"></td>      
             
            </tr>
          
          <?php } ?>
          </tbody>
          </table>

          <div class="form-group row" style="padding-top: 24px;">
            <div class="col-md-12" style="text-align:center">
              <a class="btn btn-sm btn-primary" href="admin-log-emails.php?fdate=1&user=-1&myfrom=-1&myto=-1&myret=-1&model=-1&search_string=<?php echo urlencode($user_email) ;?>"  <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Όλες οι εγγραφές ...');?></a>
            </div>
          </div> 
        </div>
      </div>                  

    </div>
  </div>
</div>



<?php } ?>




<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>    
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><input id="id_users_extra_address" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php if ($row['ID']>0) echo $row['ID'];?>"></div>
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




<?php include_once 'admin-obj-send-message.php'; ?>







<div id="dialog_protypdays" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <p style="text-align:center"><b><?php echo gks_lang('Πρότυπες Ημέρες Ασφάλισης');?></b></p>
  <p>
  <?php
  
  $protypdays_campanys_array = array();
  $sql_companys = "SELECT gks_company.id_company, gks_company.company_title, gks_company.company_color
  FROM gks_company_users LEFT JOIN gks_company ON gks_company_users.company_id = gks_company.id_company
  WHERE gks_company_users.user_id=".$id."
  order by company_sortorder,company_title";
  $result_companys = $db_link->query($sql_companys);        
  if (!$result_companys) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  while ($row_companys = $result_companys->fetch_assoc()) {
    echo '<input type="radio" name="protypdays_company"
    id="protypdays_company_'.$row_companys['id_company'].'" 
    value="'.$row_companys['id_company'].'"
    style="background-color:'.$row_companys['company_color'].'"
    ><label for="protypdays_company_'.$row_companys['id_company'].'"  
    style="background-color:'.$row_companys['company_color'].'"
    >'.$row_companys['company_title'].'</label> ';
    
    $protypdays_campanys_array[$row_companys['id_company']]= array($row_companys['company_title'], $row_companys['company_color']);
    
  }  ?>
  </p>
  
  <table cellpadding=0 cellspacing=0 border=1 class="generic-table gkstable100">
    <tr style="height:30px">
      <th width="9%"  style="text-align: center;" class="table-dark"><?php echo gks_lang('Εβδ');
        ?></th> 
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="2"><?php echo gks_lang('Δευ','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="3"><?php echo gks_lang('Τρι','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="4"><?php echo gks_lang('Τετ','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="5"><?php echo gks_lang('Πεμ','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="6"><?php echo gks_lang('Παρ','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="7"><?php echo gks_lang('Σαβ','part3');?></th>
      <th width="13%" style="text-align: center;cursor:pointer;" class="cell_protypdays_th table-dark" data-id="1"><?php echo gks_lang('Κυρ','part3');?></th>
    </tr>
    <tr style="height:50px">
      <th style="text-align: center;cursor:pointer;" class="cell_protypdays_tr table-dark" data-id="1">1</th>
      <td class="cell_protypdays" id="pd_1_2" >1</td>
      <td class="cell_protypdays" id="pd_1_3" >2</td>
      <td class="cell_protypdays" id="pd_1_4" >3</td>
      <td class="cell_protypdays" id="pd_1_5" >4</td>
      <td class="cell_protypdays" id="pd_1_6" >5</td>
      <td class="cell_protypdays" id="pd_1_7" >6</td>
      <td class="cell_protypdays" id="pd_1_1" >7</td>
    </tr>
    <tr style="height:50px">
      <th style="text-align: center;cursor:pointer;" class="cell_protypdays_tr table-dark" data-id="2">2</th>
      <td class="cell_protypdays" id="pd_2_2" >8</td>
      <td class="cell_protypdays" id="pd_2_3" >9</td>
      <td class="cell_protypdays" id="pd_2_4" >10</td>
      <td class="cell_protypdays" id="pd_2_5" >11</td>
      <td class="cell_protypdays" id="pd_2_6" >12</td>
      <td class="cell_protypdays" id="pd_2_7" >13</td>
      <td class="cell_protypdays" id="pd_2_1" >14</td>
    </tr>
    <tr style="height:50px">
      <th style="text-align: center;cursor:pointer;" class="cell_protypdays_tr table-dark" data-id="3">3</th>
      <td class="cell_protypdays" id="pd_3_2" >15</td>
      <td class="cell_protypdays" id="pd_3_3" >16</td>
      <td class="cell_protypdays" id="pd_3_4" >17</td>
      <td class="cell_protypdays" id="pd_3_5" >18</td>
      <td class="cell_protypdays" id="pd_3_6" >19</td>
      <td class="cell_protypdays" id="pd_3_7" >20</td>
      <td class="cell_protypdays" id="pd_3_1" >21</td>
    </tr>
    <tr style="height:50px">
      <th style="text-align: center;cursor:pointer;" class="cell_protypdays_tr table-dark" data-id="4">4</th>
      <td class="cell_protypdays" id="pd_4_2" >22</td>
      <td class="cell_protypdays" id="pd_4_3" >23</td>
      <td class="cell_protypdays" id="pd_4_4" >24</td>
      <td class="cell_protypdays" id="pd_4_5" >25</td>
      <td class="cell_protypdays" id="pd_4_6" >26</td>
      <td class="cell_protypdays" id="pd_4_7" >27</td>
      <td class="cell_protypdays" id="pd_4_1" >28</td>
    </tr>
    <tr style="height:50px">
      <th style="text-align: center;cursor:pointer;" class="cell_protypdays_tr table-dark" data-id="5">5</th>
      <td class="cell_protypdays" id="pd_5_2" >29</td>
      <td class="cell_protypdays" id="pd_5_3" >30</td>
      <td class="cell_protypdays" id="pd_5_4" >31</td>
      <td class="cell_protypdays" id="pd_5_5" >32</td>
      <td class="cell_protypdays" id="pd_5_6" >33</td>
      <td class="cell_protypdays" id="pd_5_7" >34</td>
      <td class="cell_protypdays" id="pd_5_1" >35</td>
    </tr>
    
  </table>
    
</div>

<div id="dialog_edit_company_sxolio" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <p><?php echo gks_lang('Πληκτρολογήστε τo σχόλιο');?>:</p>
  <p>
    <textarea type="text" id="dialog_edit_company_sxolio_val" name="dialog_edit_company_sxolio_val" style="width:100%;height:100px"></textarea>
  </p>
</div>

<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>

<div id="dialog_vies" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('VIES ΕΕ Επαλήθευση αριθ. ΦΠΑ');?></div>
    </div>
    <div class="form-group row">  
      <label for="dialog_vies_country_ee" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
      <div class="col-sm-4">
        <?php
        $country_ee='';if (isset($_GET['country_ee'])) $country_ee=trim_gks($_GET['country_ee']);
        $sql_select="select country_ee,country_name,country_initials from gks_country where country_ee<>'' order by country_name";
        $result_select = $db_link->query($sql_select);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql_select);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα'));
        }
        ?>
        <select id="dialog_vies_country_ee" class="form-control  form-control-sm" style="width1: unset;display1: inline-block;">
          <option value=""></option>
          <?php
          
          while ($row_select = $result_select->fetch_assoc()) {
            echo '<option value="'.$row_select['country_ee'].'" data-ci="'.$row_select['country_initials'].'" '; 
            if ($row_select['country_ee']==$country_ee) echo ' selected ';
            echo '>'.$row_select['country_name'].'</option>';
          }
          echo '<option value="XI" data-ci="GB" '; 
          if ('GB'==$country_ee) echo ' selected ';
          echo '>'.gks_lang('Βόρεια Ιρλανδία').'</option>';
          
          ?>
        </select>
      </div>
    </div>
    <div class="form-group row">  
      <label for="dialog_vies_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_vies_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_vies_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_vies_html">
      </div>
    </div>
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='wp_users';
var from_php_activity_model='wp_users';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

 
var from_php_id=<?php echo $id;?>;
var from_php_map_latitude=<?php echo floatval($row['ma_latitude']);?>;
var from_php_map_longitude=<?php echo floatval($row['ma_longitude']);?>;




var from_php_cansendpassword=<?php echo ($cansendpassword ? 'true' : 'false');?>;
var from_php_json_encode_protypdays_array='<?php echo json_encode($protypdays_array) ;?>';
var from_php_json_encode_protypdays_campanys_array='<?php echo json_encode($protypdays_campanys_array) ;?>';
var from_php_base64_user_login='<?php echo base64_encode($user_login) ;?>';
var from_php_base64_user_pass_pure='<?php echo base64_encode(trim_gks($user_pass_pure)) ;?>';
var from_php_base64_user_email='<?php echo base64_encode($user_email) ;?>';


var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>
    
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'wp_users','delete',$id);?>;





//readonly : (from_php_perm_ret_edit ? 0 : 1), 

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  



  
});

 
 
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-users-item.js?v=<?php echo $gks_cache_version;?>"></script>


 
 


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();

gks_plugins_functions_run('admin_users_item_scripts_before_footer',array(
  'id'=>&$id,
));

include_once('_my_footer_admin.php');



