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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_lang',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['idd_lang']=-1;
  $row['id_lang']='';
  $row['lang_name'] ='';
  $row['lang_ico']='';
  $row['lang_on_backend']=0;



  $my_page_title=gks_lang('Νέα Γλώσσα');


} else {
 $sql ="SELECT gks_lang.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_lang
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_lang.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_lang.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where idd_lang = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Γλώσσα').': '.$row['lang_name'];
  $object_title=$row['lang_name'];
}

stat_record();
$nav_active_array=array('manage','manage_lang');

//$GKS_LANG_DATA_ENABLED=array('el-GR','en-US','de-DE');

//print '<pre>';print_r($GKS_LANG_DATA_ENABLED); echo serialize($GKS_LANG_DATA_ENABLED); die();


$lang_data_obj=gks_lang_data_obj_prepare('gks_lang','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Γλώσσα');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Γλώσσα');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="id_lang"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="id_lang"  value="<?php echo htmlspecialchars_gks($row['id_lang']);?>" <?php if ($row['id_lang']=='el-GR' or $row['id_lang']=='en-US') echo 'disabled';?>>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="lang_name"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="lang_name"  value="<?php echo htmlspecialchars_gks($row['lang_name']);?>">
            </div>
          </div>
          
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('lang_name'));
          ?>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="lang_ico"><?php echo gks_lang('Εικονίδιο');?>:</label>
            <div class="col-sm-4">
              <input type="text" class="form-control form-control-sm myneedsave" id="lang_ico"  value="<?php echo htmlspecialchars_gks($row['lang_ico']);?>">
            </div>
            <div class="col-sm-4" >
              <img id="img_lang_ico" src="<?php
              $row['lang_ico']=trim_gks($row['lang_ico']);
              if ( $row['lang_ico']!='') echo 'img/flags/flags_iso/32/'.$row['lang_ico'].'.png';
              ?>" style="max-height1:32px;<?php if ($row['lang_ico']=='') echo 'display:none;'; ?>"/>
              
            </div>
          </div>
          <div class="form-group row">
            <label for="lang_on_backend" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="lang_on_backend" value="1" <?php if ($row['lang_on_backend']!=0) echo ' checked '; ?> class="switchery1_this" >
            </div>
          </div>

          
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

              <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
              <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['idd_lang'];?>" data-model="gks_lang" data-backurl="admin-lang.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>            
  </div>            
</div>            

            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       


          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['idd_lang']>0) echo $row['idd_lang'];?></span></div>
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

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php
      echo getObjectRels('gks_lang',$id);
      echo getActivityObjectTable('gks_lang',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_lang','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
    </div>
    <div class="col-md-6">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          ISO Language Code Table
        </div>
        <div class="card-body" <?php echo gks_card_body('codes',false,true);?>>  

<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse: collapse" bordercolor="#C0C0C0">

<tbody><tr><td bgcolor="#CDECFE">Code</td><td bgcolor="#CDECFE">Name</td></tr>

<tr><td>af</td><td>Afrikaans</td></tr>
<tr><td>af-ZA</td><td>Afrikaans (South Africa)</td></tr>
<tr><td>ar</td><td>Arabic</td></tr>
<tr><td>ar-AE</td><td>Arabic (U.A.E.)</td></tr>
<tr><td>ar-BH</td><td>Arabic (Bahrain)</td></tr>
<tr><td>ar-DZ</td><td>Arabic (Algeria)</td></tr>
<tr><td>ar-EG</td><td>Arabic (Egypt)</td></tr>
<tr><td>ar-IQ</td><td>Arabic (Iraq)</td></tr>
<tr><td>ar-JO</td><td>Arabic (Jordan)</td></tr>
<tr><td>ar-KW</td><td>Arabic (Kuwait)</td></tr>
<tr><td>ar-LB</td><td>Arabic (Lebanon)</td></tr>
<tr><td>ar-LY</td><td>Arabic (Libya)</td></tr>
<tr><td>ar-MA</td><td>Arabic (Morocco)</td></tr>
<tr><td>ar-OM</td><td>Arabic (Oman)</td></tr>
<tr><td>ar-QA</td><td>Arabic (Qatar)</td></tr>
<tr><td>ar-SA</td><td>Arabic (Saudi Arabia)</td></tr>
<tr><td>ar-SY</td><td>Arabic (Syria)</td></tr>
<tr><td>ar-TN</td><td>Arabic (Tunisia)</td></tr>
<tr><td>ar-YE</td><td>Arabic (Yemen)</td></tr>
<tr><td>az</td><td>Azeri (Latin)</td></tr>
<tr><td>az-AZ</td><td>Azeri (Latin) (Azerbaijan)</td></tr>
<tr><td>az-AZ</td><td>Azeri (Cyrillic) (Azerbaijan)</td></tr>
<tr><td>be</td><td>Belarusian</td></tr>
<tr><td>be-BY</td><td>Belarusian (Belarus)</td></tr>
<tr><td>bg</td><td>Bulgarian</td></tr>
<tr><td>bg-BG</td><td>Bulgarian (Bulgaria)</td></tr>
<tr><td>bs-BA</td><td>Bosnian (Bosnia and Herzegovina)</td></tr>
<tr><td>ca</td><td>Catalan</td></tr>
<tr><td>ca-ES</td><td>Catalan (Spain)</td></tr>
<tr><td>cs</td><td>Czech</td></tr>
<tr><td>cs-CZ</td><td>Czech (Czech Republic)</td></tr>
<tr><td>cy</td><td>Welsh</td></tr>
<tr><td>cy-GB</td><td>Welsh (United Kingdom)</td></tr>
<tr><td>da</td><td>Danish</td></tr>
<tr><td>da-DK</td><td>Danish (Denmark)</td></tr>
<tr><td>de</td><td>German</td></tr>
<tr><td>de-AT</td><td>German (Austria)</td></tr>
<tr><td>de-CH</td><td>German (Switzerland)</td></tr>
<tr><td>de-DE</td><td>German (Germany)</td></tr>
<tr><td>de-LI</td><td>German (Liechtenstein)</td></tr>
<tr><td>de-LU</td><td>German (Luxembourg)</td></tr>
<tr><td>dv</td><td>Divehi</td></tr>
<tr><td>dv-MV</td><td>Divehi (Maldives)</td></tr>
<tr><td>el</td><td>Greek</td></tr>
<tr><td>el-GR</td><td>Greek (Greece)</td></tr>
<tr><td>en</td><td>English</td></tr>
<tr><td>en-AU</td><td>English (Australia)</td></tr>
<tr><td>en-BZ</td><td>English (Belize)</td></tr>
<tr><td>en-CA</td><td>English (Canada)</td></tr>
<tr><td>en-CB</td><td>English (Caribbean)</td></tr>
<tr><td>en-GB</td><td>English (United Kingdom)</td></tr>
<tr><td>en-IE</td><td>English (Ireland)</td></tr>
<tr><td>en-JM</td><td>English (Jamaica)</td></tr>
<tr><td>en-NZ</td><td>English (New Zealand)</td></tr>
<tr><td>en-PH</td><td>English (Republic of the Philippines)</td></tr>
<tr><td>en-TT</td><td>English (Trinidad and Tobago)</td></tr>
<tr><td>en-US</td><td>English (United States)</td></tr>
<tr><td>en-ZA</td><td>English (South Africa)</td></tr>
<tr><td>en-ZW</td><td>English (Zimbabwe)</td></tr>
<tr><td>eo</td><td>Esperanto</td></tr>
<tr><td>es</td><td>Spanish</td></tr>
<tr><td>es-AR</td><td>Spanish (Argentina)</td></tr>
<tr><td>es-BO</td><td>Spanish (Bolivia)</td></tr>
<tr><td>es-CL</td><td>Spanish (Chile)</td></tr>
<tr><td>es-CO</td><td>Spanish (Colombia)</td></tr>
<tr><td>es-CR</td><td>Spanish (Costa Rica)</td></tr>
<tr><td>es-DO</td><td>Spanish (Dominican Republic)</td></tr>
<tr><td>es-EC</td><td>Spanish (Ecuador)</td></tr>
<tr><td>es-ES</td><td>Spanish (Castilian)</td></tr>
<tr><td>es-ES</td><td>Spanish (Spain)</td></tr>
<tr><td>es-GT</td><td>Spanish (Guatemala)</td></tr>
<tr><td>es-HN</td><td>Spanish (Honduras)</td></tr>
<tr><td>es-MX</td><td>Spanish (Mexico)</td></tr>
<tr><td>es-NI</td><td>Spanish (Nicaragua)</td></tr>
<tr><td>es-PA</td><td>Spanish (Panama)</td></tr>
<tr><td>es-PE</td><td>Spanish (Peru)</td></tr>
<tr><td>es-PR</td><td>Spanish (Puerto Rico)</td></tr>
<tr><td>es-PY</td><td>Spanish (Paraguay)</td></tr>
<tr><td>es-SV</td><td>Spanish (El Salvador)</td></tr>
<tr><td>es-UY</td><td>Spanish (Uruguay)</td></tr>
<tr><td>es-VE</td><td>Spanish (Venezuela)</td></tr>
<tr><td>et</td><td>Estonian</td></tr>
<tr><td>et-EE</td><td>Estonian (Estonia)</td></tr>
<tr><td>eu</td><td>Basque</td></tr>
<tr><td>eu-ES</td><td>Basque (Spain)</td></tr>
<tr><td>fa</td><td>Farsi</td></tr>
<tr><td>fa-IR</td><td>Farsi (Iran)</td></tr>
<tr><td>fi</td><td>Finnish</td></tr>
<tr><td>fi-FI</td><td>Finnish (Finland)</td></tr>
<tr><td>fo</td><td>Faroese</td></tr>
<tr><td>fo-FO</td><td>Faroese (Faroe Islands)</td></tr>
<tr><td>fr</td><td>French</td></tr>
<tr><td>fr-BE</td><td>French (Belgium)</td></tr>
<tr><td>fr-CA</td><td>French (Canada)</td></tr>
<tr><td>fr-CH</td><td>French (Switzerland)</td></tr>
<tr><td>fr-FR</td><td>French (France)</td></tr>
<tr><td>fr-LU</td><td>French (Luxembourg)</td></tr>
<tr><td>fr-MC</td><td>French (Principality of Monaco)</td></tr>
<tr><td>gl</td><td>Galician</td></tr>
<tr><td>gl-ES</td><td>Galician (Spain)</td></tr>
<tr><td>gu</td><td>Gujarati</td></tr>
<tr><td>gu-IN</td><td>Gujarati (India)</td></tr>
<tr><td>he</td><td>Hebrew</td></tr>
<tr><td>he-IL</td><td>Hebrew (Israel)</td></tr>
<tr><td>hi</td><td>Hindi</td></tr>
<tr><td>hi-IN</td><td>Hindi (India)</td></tr>
<tr><td>hr</td><td>Croatian</td></tr>
<tr><td>hr-BA</td><td>Croatian (Bosnia and Herzegovina)</td></tr>
<tr><td>hr-HR</td><td>Croatian (Croatia)</td></tr>
<tr><td>hu</td><td>Hungarian</td></tr>
<tr><td>hu-HU</td><td>Hungarian (Hungary)</td></tr>
<tr><td>hy</td><td>Armenian</td></tr>
<tr><td>hy-AM</td><td>Armenian (Armenia)</td></tr>
<tr><td>id</td><td>Indonesian</td></tr>
<tr><td>id-ID</td><td>Indonesian (Indonesia)</td></tr>
<tr><td>is</td><td>Icelandic</td></tr>
<tr><td>is-IS</td><td>Icelandic (Iceland)</td></tr>
<tr><td>it</td><td>Italian</td></tr>
<tr><td>it-CH</td><td>Italian (Switzerland)</td></tr>
<tr><td>it-IT</td><td>Italian (Italy)</td></tr>
<tr><td>ja</td><td>Japanese</td></tr>
<tr><td>ja-JP</td><td>Japanese (Japan)</td></tr>
<tr><td>ka</td><td>Georgian</td></tr>
<tr><td>ka-GE</td><td>Georgian (Georgia)</td></tr>
<tr><td>kk</td><td>Kazakh</td></tr>
<tr><td>kk-KZ</td><td>Kazakh (Kazakhstan)</td></tr>
<tr><td>kn</td><td>Kannada</td></tr>
<tr><td>kn-IN</td><td>Kannada (India)</td></tr>
<tr><td>ko</td><td>Korean</td></tr>
<tr><td>ko-KR</td><td>Korean (Korea)</td></tr>
<tr><td>kok</td><td>Konkani</td></tr>
<tr><td>kok-IN</td><td>Konkani (India)</td></tr>
<tr><td>ky</td><td>Kyrgyz</td></tr>
<tr><td>ky-KG</td><td>Kyrgyz (Kyrgyzstan)</td></tr>
<tr><td>lt</td><td>Lithuanian</td></tr>
<tr><td>lt-LT</td><td>Lithuanian (Lithuania)</td></tr>
<tr><td>lv</td><td>Latvian</td></tr>
<tr><td>lv-LV</td><td>Latvian (Latvia)</td></tr>
<tr><td>mi</td><td>Maori</td></tr>
<tr><td>mi-NZ</td><td>Maori (New Zealand)</td></tr>
<tr><td>mk</td><td>FYRO Macedonian</td></tr>
<tr><td>mk-MK</td><td>FYRO Macedonian (Former Yugoslav Republic of Macedonia)</td></tr>
<tr><td>mn</td><td>Mongolian</td></tr>
<tr><td>mn-MN</td><td>Mongolian (Mongolia)</td></tr>
<tr><td>mr</td><td>Marathi</td></tr>
<tr><td>mr-IN</td><td>Marathi (India)</td></tr>
<tr><td>ms</td><td>Malay</td></tr>
<tr><td>ms-BN</td><td>Malay (Brunei Darussalam)</td></tr>
<tr><td>ms-MY</td><td>Malay (Malaysia)</td></tr>
<tr><td>mt</td><td>Maltese</td></tr>
<tr><td>mt-MT</td><td>Maltese (Malta)</td></tr>
<tr><td>nb</td><td>Norwegian (Bokm?l)</td></tr>
<tr><td>nb-NO</td><td>Norwegian (Bokm?l) (Norway)</td></tr>
<tr><td>nl</td><td>Dutch</td></tr>
<tr><td>nl-BE</td><td>Dutch (Belgium)</td></tr>
<tr><td>nl-NL</td><td>Dutch (Netherlands)</td></tr>
<tr><td>nn-NO</td><td>Norwegian (Nynorsk) (Norway)</td></tr>
<tr><td>ns</td><td>Northern Sotho</td></tr>
<tr><td>ns-ZA</td><td>Northern Sotho (South Africa)</td></tr>
<tr><td>pa</td><td>Punjabi</td></tr>
<tr><td>pa-IN</td><td>Punjabi (India)</td></tr>
<tr><td>pl</td><td>Polish</td></tr>
<tr><td>pl-PL</td><td>Polish (Poland)</td></tr>
<tr><td>ps</td><td>Pashto</td></tr>
<tr><td>ps-AR</td><td>Pashto (Afghanistan)</td></tr>
<tr><td>pt</td><td>Portuguese</td></tr>
<tr><td>pt-BR</td><td>Portuguese (Brazil)</td></tr>
<tr><td>pt-PT</td><td>Portuguese (Portugal)</td></tr>
<tr><td>qu</td><td>Quechua</td></tr>
<tr><td>qu-BO</td><td>Quechua (Bolivia)</td></tr>
<tr><td>qu-EC</td><td>Quechua (Ecuador)</td></tr>
<tr><td>qu-PE</td><td>Quechua (Peru)</td></tr>
<tr><td>ro</td><td>Romanian</td></tr>
<tr><td>ro-RO</td><td>Romanian (Romania)</td></tr>
<tr><td>ru</td><td>Russian</td></tr>
<tr><td>ru-RU</td><td>Russian (Russia)</td></tr>
<tr><td>sa</td><td>Sanskrit</td></tr>
<tr><td>sa-IN</td><td>Sanskrit (India)</td></tr>
<tr><td>se</td><td>Sami (Northern)</td></tr>
<tr><td>se-FI</td><td>Sami (Northern) (Finland)</td></tr>
<tr><td>se-FI</td><td>Sami (Skolt) (Finland)</td></tr>
<tr><td>se-FI</td><td>Sami (Inari) (Finland)</td></tr>
<tr><td>se-NO</td><td>Sami (Northern) (Norway)</td></tr>
<tr><td>se-NO</td><td>Sami (Lule) (Norway)</td></tr>
<tr><td>se-NO</td><td>Sami (Southern) (Norway)</td></tr>
<tr><td>se-SE</td><td>Sami (Northern) (Sweden)</td></tr>
<tr><td>se-SE</td><td>Sami (Lule) (Sweden)</td></tr>
<tr><td>se-SE</td><td>Sami (Southern) (Sweden)</td></tr>
<tr><td>sk</td><td>Slovak</td></tr>
<tr><td>sk-SK</td><td>Slovak (Slovakia)</td></tr>
<tr><td>sl</td><td>Slovenian</td></tr>
<tr><td>sl-SI</td><td>Slovenian (Slovenia)</td></tr>
<tr><td>sq</td><td>Albanian</td></tr>
<tr><td>sq-AL</td><td>Albanian (Albania)</td></tr>
<tr><td>sr-BA</td><td>Serbian (Latin) (Bosnia and Herzegovina)</td></tr>
<tr><td>sr-BA</td><td>Serbian (Cyrillic) (Bosnia and Herzegovina)</td></tr>
<tr><td>sr-SP</td><td>Serbian (Latin) (Serbia and Montenegro)</td></tr>
<tr><td>sr-SP</td><td>Serbian (Cyrillic) (Serbia and Montenegro)</td></tr>
<tr><td>sv</td><td>Swedish</td></tr>
<tr><td>sv-FI</td><td>Swedish (Finland)</td></tr>
<tr><td>sv-SE</td><td>Swedish (Sweden)</td></tr>
<tr><td>sw</td><td>Swahili</td></tr>
<tr><td>sw-KE</td><td>Swahili (Kenya)</td></tr>
<tr><td>syr</td><td>Syriac</td></tr>
<tr><td>syr-SY</td><td>Syriac (Syria)</td></tr>
<tr><td>ta</td><td>Tamil</td></tr>
<tr><td>ta-IN</td><td>Tamil (India)</td></tr>
<tr><td>te</td><td>Telugu</td></tr>
<tr><td>te-IN</td><td>Telugu (India)</td></tr>
<tr><td>th</td><td>Thai</td></tr>
<tr><td>th-TH</td><td>Thai (Thailand)</td></tr>
<tr><td>tl</td><td>Tagalog</td></tr>
<tr><td>tl-PH</td><td>Tagalog (Philippines)</td></tr>
<tr><td>tn</td><td>Tswana</td></tr>
<tr><td>tn-ZA</td><td>Tswana (South Africa)</td></tr>
<tr><td>tr</td><td>Turkish</td></tr>
<tr><td>tr-TR</td><td>Turkish (Turkey)</td></tr>
<tr><td>tt</td><td>Tatar</td></tr>
<tr><td>tt-RU</td><td>Tatar (Russia)</td></tr>
<tr><td>ts</td><td>Tsonga</td></tr>
<tr><td>uk</td><td>Ukrainian</td></tr>
<tr><td>uk-UA</td><td>Ukrainian (Ukraine)</td></tr>
<tr><td>ur</td><td>Urdu</td></tr>
<tr><td>ur-PK</td><td>Urdu (Islamic Republic of Pakistan)</td></tr>
<tr><td>uz</td><td>Uzbek (Latin)</td></tr>
<tr><td>uz-UZ</td><td>Uzbek (Latin) (Uzbekistan)</td></tr>
<tr><td>uz-UZ</td><td>Uzbek (Cyrillic) (Uzbekistan)</td></tr>
<tr><td>vi</td><td>Vietnamese</td></tr>
<tr><td>vi-VN</td><td>Vietnamese (Viet Nam)</td></tr>
<tr><td>xh</td><td>Xhosa</td></tr>
<tr><td>xh-ZA</td><td>Xhosa (South Africa)</td></tr>
<tr><td>zh</td><td>Chinese</td></tr>
<tr><td>zh-CN</td><td>Chinese (S)</td></tr>
<tr><td>zh-HK</td><td>Chinese (Hong Kong)</td></tr>
<tr><td>zh-MO</td><td>Chinese (Macau)</td></tr>
<tr><td>zh-SG</td><td>Chinese (Singapore)</td></tr>
<tr><td>zh-TW</td><td>Chinese (T)</td></tr>
<tr><td>zu</td><td>Zulu</td></tr>
<tr><td>zu-ZA</td><td>Zulu (South Africa)</td></tr>
</tbody></table>          

        </div>
      </div>
    </div>
  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



var from_php_dialog_object_rel_curr='gks_lang';
var from_php_activity_model='gks_lang';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_lang','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_lang','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_lang','delete',$id);?>;

 
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });  

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';

    datasend+='&id_lang='  +  encodeURIComponent($.base64.encode($("#mypostform #id_lang").val().trim()));
    datasend+='&lang_name='  +  encodeURIComponent($.base64.encode($("#mypostform #lang_name").val().trim()));
    datasend+='&lang_ico='  +  encodeURIComponent($.base64.encode($("#mypostform #lang_ico").val().trim()));
    datasend+='&lang_on_backend=' + (($('#lang_on_backend').is(':checked')) ? '1':'0');
    
    
    datasend+=gks_lang_data_obj_input_collect();
    //console.log(datasend);
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-lang-item-exec.php?id=' + <?php echo $id;?>,
			type: 'POST',
			cache: false,
			dataType: 'json',
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
					  need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  
  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
  $('#lang_ico').on(mychange, function() {
    vvv=$(this).val().trim();
    if (vvv=='') {
      $('#img_lang_ico').attr('src','').hide();
    } else {
      $('#img_lang_ico').attr('src','img/flags/flags_iso/32/' + vvv + '.png').show();
    }
    
    
  });
  
  
  
  
  
  
  
  
  
  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;    

    
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


