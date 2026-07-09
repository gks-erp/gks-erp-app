<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*

$cache_url
$cache_filename=$gks_user_cache_version_prefix.substr($cache_filename, 0, strlen($cache_filename)-4.).'.js';


*/

$gks_user_cache_version_prefix='0_0_0_0';



function gks_cache_update_menu_version($user_id=0) {
  global $my_wp_user_id;
  global $db_link;
  if ($user_id==0) $user_id=$my_wp_user_id;
  if ($user_id>0) {
    $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_menu_version=".time()." where ID=".$user_id;
  } else {
    $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_menu_version=".time();
  }
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

}



function gks_cache_country() {
  global $db_link;
  global $gks_user_cache_version_prefix;
  global $GKS_LANG_DEFAULT;
  if ($GKS_LANG_DEFAULT=='') return;
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'gks_country.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  
  if (file_exists($cache_fs) == false) {
    
    $savejs='';
    $savejs.='var gks_country=[];'."\n";

    $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
    gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));

    
    $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
    FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
    ".$lang_prepare_gks_country['sql']['from2']."
    ORDER BY ".gks_lang_sql_field('country_name',$lang_prepare_gks_country,'',true);
    //echo '<pre>ssssssssss ';echo $sql;die();
    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    while ($row = $result->fetch_assoc()) {
      $savejs.='gks_country.push({id_country:'.$row['id_country'].',country_ee:\''.$row['country_ee'].'\',country_name:\''.base64_encode($row['country_name']).'\',country_initials:\''.$row['country_initials'].'\'})'.";\n";
    }
    
    $savejs.='
for(i=0;i<gks_country.length;i++) {
  gks_country[i].country_name=$.base64.decode(gks_country[i].country_name);
  
}
//console.log(gks_country);
';
    
    file_put_contents($cache_fs, $savejs);
  }
  
}

function gks_cache_lang() {
  global $db_link;
  global $gks_user_cache_version_prefix;
  global $GKS_LANG_DEFAULT;
  if ($GKS_LANG_DEFAULT=='') return;
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
//  $cache_filename='gks_lang.'.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'.js';
  $cache_filename=$gks_user_cache_version_prefix.'gks_langs.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  
  if (file_exists($cache_fs) == false) {
    
    $savejs='';
    $savejs.='var gks_langs=[];'."\n";
    
    $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
    gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
    $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
    FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
    ".$lang_prepare_gks_lang['sql']['from2']."
    ORDER BY lang_sortorder,lang_name";
    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    while ($row = $result->fetch_assoc()) {
      $savejs.='gks_langs.push({id_lang:\''.$row['id_lang'].'\',lang_name:\''.base64_encode($row['lang_name']).'\',lang_ico:\''.$row['lang_ico'].'\'})'.";\n";
    }


    
    $savejs.='
    
for(i=0;i<gks_langs.length;i++) {
  gks_langs[i].lang_name=$.base64.decode(gks_langs[i].lang_name);
}
//console.log(gks_lang);
';


    
    file_put_contents($cache_fs, $savejs);
  }
  
}

function gks_cache_lang_data() {
  global $db_link;
  global $gks_user_cache_version_prefix;

  global $gks_user_settings;
  
  $sql="select id_lang FROM gks_lang where id_lang<>'el-GR' order by id_lang";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  $langs=[];
  while ($row = $result->fetch_assoc()) {
    $langs[]=$row['id_lang'];
  }  
  $files_list=['generic','part2','part3','part4'];
  foreach ($langs as $id_lang) {
    foreach ($files_list as $file) {
       // loop through values 
    
      $file_lang=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/languages/'.$id_lang.'/'.$file.'.php';
      if (file_exists($file_lang)) {
        //echo $file_lang;die();
        
        $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
        $cache_filename=$gks_user_cache_version_prefix.'gks_lang_data_'.$id_lang.'_'.$file.'.js';
        $cache_fs=$cache_dir.$cache_filename;

        //if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
        
        if (file_exists($cache_fs) == false) {
          if (isset($gks_lang_array)) unset($gks_lang_array);
          $gks_lang_array=[];
          include $file_lang;
          if (isset($gks_lang_array[$file])) {
            
            $savejs='var gks_lang_data_js'.($file=='generic'?'':'_'.$file).'={'."\r\n";
            
            if ($file=='part4') {
              foreach ($gks_lang_array[$file] as $vkey => $vdata) {
                $myarray='';
                foreach ($vdata as $vkey2 => $vdata2) {
                  $vkey2=str_replace("'","\\'",$vkey2);
                  $vdata2=str_replace("'","\\'",$vdata2);
                  $myarray.=" '".$vkey2."':'".$vdata2."',"."\r\n";
                }
                $vkey=str_replace("'","\\'",$vkey);
                $savejs.="'".$vkey."':{\r\n".$myarray."},"."\r\n";
              }
            } else {
              foreach ($gks_lang_array[$file] as $vkey => $vdata) {
                $vkey=str_replace("'","\\'",$vkey);
                $vdata=str_replace("'","\\'",$vdata);
                $savejs.="'".$vkey."':'".$vdata."',"."\r\n";
              }
            }
            $savejs.='}'."\r\n";
            file_put_contents($cache_fs, $savejs);
          }
        }
      }
    }
  }
}

function gks_cache_common_js() {
  //global $db_link;
  global $gks_user_cache_version_prefix;

  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
//  $cache_filename='gks_lang.'.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'.js';
  $cache_filename=$gks_user_cache_version_prefix.'gks_common.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  
  if (file_exists($cache_fs) == false) {
    
    $savejs='';
    $savejs.='var gks_image_extension=[';
    $arrr=[];
    foreach (GKS_IMAGE_EXTENSION as $value) {
      $arrr[]="'".$value."'";
    } 
    $savejs.=implode(',',$arrr).'];'."\n";
    file_put_contents($cache_fs, $savejs);
  }
    
}

function gks_cache_acc_inv_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;
    
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-acc-inv-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  
  
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT id_acc_eidi_parastatikon_type as id, acc_eidi_parastatikon_type_descr as descr, antisimvalomenos_label as label
    FROM gks_acc_eidi_parastatikon_types
    ORDER BY id_acc_eidi_parastatikon_type;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $eidi_parastatikon_types=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $eidi_parastatikon_types[]=$row_extra;
    }
    $savejs.='var eidi_parastatikon_types=[];'."\n";
    foreach ($eidi_parastatikon_types as $value) {
      $savejs.='eidi_parastatikon_types.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\',label:\''.base64_encode($value['label']).'\'})'.";\n";
    } 
  
    
    $sql_extra="SELECT id_aade_katigoria_parakratoumemenon_foron as id, aade_katigoria_parakratoumemenon_foron_descr as descr, 
    aade_katigoria_parakratoumemenon_foron_type as type
    FROM gks_aade_katigoria_parakratoumemenon_foron
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_parakratoumemenon_foron=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_parakratoumemenon_foron[]=$row_extra;
    }
    $savejs.='var katigoria_parakratoumemenon_foron=[];'."\n";
    foreach ($katigoria_parakratoumemenon_foron as $value) {
      $savejs.='katigoria_parakratoumemenon_foron.push({id:'.$value['id'].',type:\''.$value['type'].'\',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    }
    
    
    $sql_extra="SELECT id_aade_katigoria_loipon_foron as id, aade_katigoria_loipon_foron_descr as descr, 
    aade_katigoria_loipon_foron_type as type
    FROM gks_aade_katigoria_loipon_foron
    where aade_disable=0
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_loipon_foron=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_loipon_foron[]=$row_extra;
    }
    $savejs.='var katigoria_loipon_foron=[];'."\n";
    foreach ($katigoria_loipon_foron as $value) {
      $savejs.='katigoria_loipon_foron.push({id:'.$value['id'].',type:\''.$value['type'].'\',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    
    $sql_extra="SELECT id_aade_katigoria_xartosimou as id, aade_katigoria_xartosimou_descr as descr, 
    aade_katigoria_xartosimou_type as type
    FROM gks_aade_katigoria_xartosimou
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_xartosimou=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_xartosimou[]=$row_extra;
    }
    $savejs.='var katigoria_xartosimou=[];'."\n";
    foreach ($katigoria_xartosimou as $value) {
      $savejs.='katigoria_xartosimou.push({id:'.$value['id'].',type:\''.$value['type'].'\',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_katigoria_telon as id, aade_katigoria_telon_descr as descr, 
    aade_katigoria_telon_type as type
    FROM gks_aade_katigoria_telon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_telon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_telon[]=$row_extra;
    }
    $savejs.='var katigoria_telon=[];'."\n";
    foreach ($katigoria_telon as $value) {
      $savejs.='katigoria_telon.push({id:'.$value['id'].',type:\''.$value['type'].'\',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
  
    
    $sql_extra="SELECT id_aade_katigoria_xarakt_esodon as id, aade_katigoria_xarakt_esodon_descr as descr
    FROM gks_aade_katigoria_xarakt_esodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_xarakt_esodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_xarakt_esodon[]=$row_extra;
    }
    $savejs.='var katigoria_xarakt_esodon=[];'."\n";
    foreach ($katigoria_xarakt_esodon as $value) {
      $savejs.='katigoria_xarakt_esodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_typos_xarakt_esodon as id, aade_typos_xarakt_esodon_descr as descr,
    aade_typos_xarakt_esodon_code as code
    FROM gks_aade_typos_xarakt_esodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $typos_xarakt_esodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      if (!empty($row_extra['code']) and $row_extra['code']!='e3_null') $row_extra['descr'].=' ('.trim_gks($row_extra['code']).')';
      $typos_xarakt_esodon[]=$row_extra;
    }
    $savejs.='var typos_xarakt_esodon=[];'."\n";
    foreach ($typos_xarakt_esodon as $value) {
      $savejs.='typos_xarakt_esodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_katigoria_xarakt_eksodon as id, aade_katigoria_xarakt_eksodon_descr as descr
    FROM gks_aade_katigoria_xarakt_eksodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_xarakt_eksodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_xarakt_eksodon[]=$row_extra;
    }
    $savejs.='var katigoria_xarakt_eksodon=[];'."\n";
    foreach ($katigoria_xarakt_eksodon as $value) {
      $savejs.='katigoria_xarakt_eksodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_typos_xarakt_eksodon as id, aade_typos_xarakt_eksodon_descr as descr,
    aade_typos_xarakt_eksodon_code as code
    FROM gks_aade_typos_xarakt_eksodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $typos_xarakt_eksodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      if (!empty($row_extra['code']) and $row_extra['code']!='e3_null') $row_extra['descr'].=' ('.trim_gks($row_extra['code']).')';
      $typos_xarakt_eksodon[]=$row_extra;
    }
    $savejs.='var typos_xarakt_eksodon=[];'."\n";
    foreach ($typos_xarakt_eksodon as $value) {
      $savejs.='typos_xarakt_eksodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    }
    
    
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon AS c
    FROM (gks_aade_xarakt_sindiasmoi_esodon LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_katigoria_xarakt_esodon_code = gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_esodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon)>0))
    GROUP BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon;";
  
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_esodon1=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_esodon1[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_esodon1=[];'."\n";
    foreach ($xarakt_sindiasmoi_esodon1 as $value) {
      $savejs.='xarakt_sindiasmoi_esodon1.push({p:'.$value['p'].',c:'.$value['c'].'})'.";\n";
    } 
    
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon AS c, gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon AS t
    FROM ((gks_aade_xarakt_sindiasmoi_esodon LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_katigoria_xarakt_esodon_code = gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code) LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_typos_xarakt_esodon_code = gks_aade_typos_xarakt_esodon.aade_typos_xarakt_esodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_esodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon)>0) AND ((gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon)>0))
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon, gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_esodon2=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_esodon2[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_esodon2=[];'."\n";
    foreach ($xarakt_sindiasmoi_esodon2 as $value) {
      $savejs.='xarakt_sindiasmoi_esodon2.push({p:'.$value['p'].',c:'.$value['c'].',t:'.$value['t'].'})'.";\n";
    } 
    
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon AS c
    FROM (gks_aade_xarakt_sindiasmoi_eksodon LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_katigoria_xarakt_eksodon_code = gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon)>0))
    GROUP BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_eksodon1=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_eksodon1[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_eksodon1=[];'."\n";
    foreach ($xarakt_sindiasmoi_eksodon1 as $value) {
      $savejs.='xarakt_sindiasmoi_eksodon1.push({p:'.$value['p'].',c:'.$value['c'].'})'.";\n";
    }    
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon AS c, gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon AS t
    FROM ((gks_aade_xarakt_sindiasmoi_eksodon LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_katigoria_xarakt_eksodon_code = gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code) LEFT JOIN gks_aade_typos_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_typos_xarakt_eksodon_code = gks_aade_typos_xarakt_eksodon.aade_typos_xarakt_eksodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon)>0) AND ((gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon)>0))
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon, gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_eksodon2=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_eksodon2[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_eksodon2=[];'."\n";
    foreach ($xarakt_sindiasmoi_eksodon2 as $value) {
      $savejs.='xarakt_sindiasmoi_eksodon2.push({p:'.$value['p'].',c:'.$value['c'].',t:'.$value['t'].'})'.";\n";
    } 
  
    $sql_extra="SELECT id_aade_katigoria_fpa_ejeresi as id, aade_katigoria_fpa_ejeresi_descr as descr
    FROM gks_aade_katigoria_fpa_ejeresi
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_fpa_ejeresi=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_fpa_ejeresi[]=$row_extra;
    }
    $savejs.='var katigoria_fpa_ejeresi=[];'."\n";
    foreach ($katigoria_fpa_ejeresi as $value) {
      $savejs.='katigoria_fpa_ejeresi.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
  
  
    $sql_entitytype="SELECT id_aade_entitytype as id, aade_entitytype_descr as descr
    FROM gks_aade_entitytype
    ORDER BY sortorder;";
    $result_entitytype = $db_link->query($sql_entitytype);        
    if (!$result_entitytype) {debug_mail(false,'error sql',$sql_entitytype); die('sql error');}
    $aade_entitytype=array();
    while ($row_entitytype = $result_entitytype->fetch_assoc()) {
      $aade_entitytype[]=$row_entitytype;
    }
    $savejs.='var aade_entitytype=[];'."\n";
    foreach ($aade_entitytype as $value) {
      $savejs.='aade_entitytype.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    }     
    
    
    file_put_contents($cache_fs, $savejs);
  }  
}



function gks_cache_admin_orders_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;
    
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-orders-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT id_acc_eidi_parastatikon_type as id, acc_eidi_parastatikon_type_descr as descr, antisimvalomenos_label as label
    FROM gks_acc_eidi_parastatikon_types
    ORDER BY id_acc_eidi_parastatikon_type;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $eidi_parastatikon_types=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $eidi_parastatikon_types[]=$row_extra;
    }
    $savejs.='var eidi_parastatikon_types=[];'."\n";
    foreach ($eidi_parastatikon_types as $value) {
      $savejs.='eidi_parastatikon_types.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\',label:\''.base64_encode($value['label']).'\'})'.";\n";
    } 
  
    file_put_contents($cache_fs, $savejs);
  }  
}



function gks_cache_admin_acc_pay_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;

  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-acc-pay-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT id_acc_eidi_parastatikon_type as id, acc_eidi_parastatikon_type_descr as descr, antisimvalomenos_label as label
    FROM gks_acc_eidi_parastatikon_types
    ORDER BY id_acc_eidi_parastatikon_type;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $eidi_parastatikon_types=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $eidi_parastatikon_types[]=$row_extra;
    }
    $savejs.='var eidi_parastatikon_types=[];'."\n";
    foreach ($eidi_parastatikon_types as $value) {
      $savejs.='eidi_parastatikon_types.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\',label:\''.base64_encode($value['label']).'\'})'.";\n";
    } 
    file_put_contents($cache_fs, $savejs);
  }
  
}


function gks_cache_admin_products_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;

  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-products-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.*,
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
    
    where 1=1 
    ORDER BY gks_acc_eidi_parastatikon.sortorder,fullpath";
  
    $result_select = $db_link->query($sql_extra);        
    if (!$result_select) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $isgroup_open=false;
    $eidi_parastatikon_str='';
    while ($row_select = $result_select->fetch_assoc()) {
      $mypad=''; 
      if (!empty($row_select['gt2'])) $mypad='&nbsp;&nbsp;&nbsp;';
      if (!empty($row_select['gt3'])) $mypad='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      
      
      if ($row_select['is_selectable']==0) {
        if ($isgroup_open) $eidi_parastatikon_str.= '</optgroup>'."\n";
        $isgroup_open=true;
        $eidi_parastatikon_str.= '<optgroup label="'.$mypad.$row_select['eidos_parastatikou_descr'].'">'."\n";
      } else {
        $eidi_parastatikon_str.= '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" ';
        $eidi_parastatikon_str.= '>'.$mypad.$row_select['eidos_parastatikou_descr'].'</option>'."\n";
      }
    }
    if ($isgroup_open) $eidi_parastatikon_str.= '</optgroup>';
    
    $savejs.='var eidi_parastatikon_str=\''.base64_encode($eidi_parastatikon_str).'\''.";\n";
    
  
    $sql_extra="SELECT id_aade_katigoria_xarakt_esodon as id, aade_katigoria_xarakt_esodon_descr as descr
    FROM gks_aade_katigoria_xarakt_esodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_xarakt_esodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_xarakt_esodon[]=$row_extra;
    }
    $savejs.='var katigoria_xarakt_esodon=[];'."\n";
    foreach ($katigoria_xarakt_esodon as $value) {
      $savejs.='katigoria_xarakt_esodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_typos_xarakt_esodon as id, aade_typos_xarakt_esodon_descr as descr,
    aade_typos_xarakt_esodon_code as code
    FROM gks_aade_typos_xarakt_esodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $typos_xarakt_esodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      if (!empty($row_extra['code']) and $row_extra['code']!='e3_null') $row_extra['descr'].=' ('.trim_gks($row_extra['code']).')';
      $typos_xarakt_esodon[]=$row_extra;
    }
    $savejs.='var typos_xarakt_esodon=[];'."\n";
    foreach ($typos_xarakt_esodon as $value) {
      $savejs.='typos_xarakt_esodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_katigoria_xarakt_eksodon as id, aade_katigoria_xarakt_eksodon_descr as descr
    FROM gks_aade_katigoria_xarakt_eksodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $katigoria_xarakt_eksodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $katigoria_xarakt_eksodon[]=$row_extra;
    }
    $savejs.='var katigoria_xarakt_eksodon=[];'."\n";
    foreach ($katigoria_xarakt_eksodon as $value) {
      $savejs.='katigoria_xarakt_eksodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    } 
    
    $sql_extra="SELECT id_aade_typos_xarakt_eksodon as id, aade_typos_xarakt_eksodon_descr as descr,
    aade_typos_xarakt_eksodon_code as code
    FROM gks_aade_typos_xarakt_eksodon
    ORDER BY sortorder;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $typos_xarakt_eksodon=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      if (!empty($row_extra['code']) and $row_extra['code']!='e3_null') $row_extra['descr'].=' ('.trim_gks($row_extra['code']).')';
      $typos_xarakt_eksodon[]=$row_extra;
    }
    $savejs.='var typos_xarakt_eksodon=[];'."\n";
    foreach ($typos_xarakt_eksodon as $value) {
      $savejs.='typos_xarakt_eksodon.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    }
    
    
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon AS c
    FROM (gks_aade_xarakt_sindiasmoi_esodon LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_katigoria_xarakt_esodon_code = gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_esodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon)>0))
    GROUP BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_esodon1=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_esodon1[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_esodon1=[];'."\n";
    foreach ($xarakt_sindiasmoi_esodon1 as $value) {
      $savejs.='xarakt_sindiasmoi_esodon1.push({p:'.$value['p'].',c:'.$value['c'].'})'.";\n";
    } 
  
    
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon AS c, gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon AS t
    FROM ((gks_aade_xarakt_sindiasmoi_esodon LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_katigoria_xarakt_esodon_code = gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code) LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_aade_xarakt_sindiasmoi_esodon.aade_typos_xarakt_esodon_code = gks_aade_typos_xarakt_esodon.aade_typos_xarakt_esodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_esodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon)>0) AND ((gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon)>0))
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon, gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_esodon2=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_esodon2[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_esodon2=[];'."\n";
    foreach ($xarakt_sindiasmoi_esodon2 as $value) {
      $savejs.='xarakt_sindiasmoi_esodon2.push({p:'.$value['p'].',c:'.$value['c'].',t:'.$value['t'].'})'.";\n";
    } 
    
  
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon AS c
    FROM (gks_aade_xarakt_sindiasmoi_eksodon LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_katigoria_xarakt_eksodon_code = gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon)>0))
    GROUP BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_eksodon1=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_eksodon1[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_eksodon1=[];'."\n";
    foreach ($xarakt_sindiasmoi_eksodon1 as $value) {
      $savejs.='xarakt_sindiasmoi_eksodon1.push({p:'.$value['p'].',c:'.$value['c'].'})'.";\n";
    }    
  
  
  
  
    $sql_extra="SELECT gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou AS p, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon AS c, gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon AS t
    FROM ((gks_aade_xarakt_sindiasmoi_eksodon LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_katigoria_xarakt_eksodon_code = gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code) LEFT JOIN gks_aade_typos_xarakt_eksodon ON gks_aade_xarakt_sindiasmoi_eksodon.aade_typos_xarakt_eksodon_code = gks_aade_typos_xarakt_eksodon.aade_typos_xarakt_eksodon_code) LEFT JOIN gks_acc_eidi_parastatikon ON gks_aade_xarakt_sindiasmoi_eksodon.sind_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE (((gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)>0) AND ((gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon)>0) AND ((gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon)>0))
    ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon, gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $xarakt_sindiasmoi_eksodon2=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $xarakt_sindiasmoi_eksodon2[]=$row_extra;
    }
    $savejs.='var xarakt_sindiasmoi_eksodon2=[];'."\n";
    foreach ($xarakt_sindiasmoi_eksodon2 as $value) {
      $savejs.='xarakt_sindiasmoi_eksodon2.push({p:'.$value['p'].',c:'.$value['c'].',t:'.$value['t'].'})'.";\n";
    } 
      
    file_put_contents($cache_fs, $savejs);
  
  }
  
}


function gks_cache_idiotites_js_get_url() {
  global $db_link;
  global $GKS_IDIOTITES_CACHE_VER;
  global $gks_user_cache_version_prefix;
  
  
  if ($GKS_IDIOTITES_CACHE_VER==0) {
    $GKS_IDIOTITES_CACHE_VER=time();
    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
  }
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'gks_product_idiotites.'.$GKS_IDIOTITES_CACHE_VER.'.js';
  $cache_fs=$cache_dir.$cache_filename;
  //if (1==1 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  $cache_url='/my/cache/'.$cache_filename;
  
  if (file_exists($cache_fs) == false) {  

    $sql="SELECT id_product_idiotita as id, idiotita_name as `name`, idiotita_type as `type`
    FROM gks_product_idiotites
    ORDER BY gks_product_idiotites.idiotita_sortorder, gks_product_idiotites.idiotita_name;";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
    $data=array();$ids=array();
    while ($row = $result->fetch_assoc()) {
      $ids[]=$row['id'];
      $row['terms']=array();
      $data[$row['id']]= $row;
    }
    if (count($ids)>0) {
      $sql="SELECT id_product_idiotita_term, idiotita_term_name,idiotita_term_button,idiotita_term_color,idiotita_term_image,idiotita_id
      FROM gks_product_idiotites_terms
      where idiotita_id in (".implode(',',$ids).")
      ORDER BY idiotita_term_sortorder";
      //echo $sql;die();
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
      while ($row = $result->fetch_assoc()) {
        if (isset($data[$row['idiotita_id']])) {
          $data[$row['idiotita_id']]['terms'][]=array(
            'id'=>$row['id_product_idiotita_term'],
            'name'=>$row['idiotita_term_name'],
            'button'=>$row['idiotita_term_button'],
            'color'=>$row['idiotita_term_color'],
            'image'=>$row['idiotita_term_image'],
          );
        }
      }
    }
    
    $savejs='';
    $savejs.='var gks_product_idiotites=[];'."\n";
    foreach ($data as $row) {
      if (count($row['terms'])>0) {
        $savejs.='terms=[];'."\n";
        $savejs.='termsf=[];'."\n";
        foreach ($row['terms'] as $term) {
          $savejs.='terms.push(\''.base64_encode($term['name']).'\')'.";\n";
          $savejs.='termsf.push({id:'.$term['id'].',name:\''.base64_encode($term['name']).'\',button:\''.base64_encode($term['button']).'\',color:\''.base64_encode($term['color']).'\',image:\''.base64_encode($term['image']).'\'})'.";\n";
        } 
        $savejs.='gks_product_idiotites['.$row['id'].']={id:'.$row['id'].',name:\''.base64_encode($row['name']).'\',type:\''.$row['type'].'\',terms:terms,termsf:termsf}'.";\n";
      }
    }
    
    $savejs.='
gks_product_idiotites.forEach(function(item, i) {
  gks_product_idiotites[i].name=$.base64.decode(gks_product_idiotites[i].name);
  for(j=0;j<gks_product_idiotites[i].terms.length;j++) {
    gks_product_idiotites[i].terms[j]=$.base64.decode(gks_product_idiotites[i].terms[j]);
  }
  for(j=0;j<gks_product_idiotites[i].termsf.length;j++) {
    gks_product_idiotites[i].termsf[j].name=$.base64.decode(gks_product_idiotites[i].termsf[j].name);
    gks_product_idiotites[i].termsf[j].button=$.base64.decode(gks_product_idiotites[i].termsf[j].button);
    gks_product_idiotites[i].termsf[j].color=$.base64.decode(gks_product_idiotites[i].termsf[j].color);
    gks_product_idiotites[i].termsf[j].image=$.base64.decode(gks_product_idiotites[i].termsf[j].image);
  }
});
//console.log(gks_product_idiotites);
';


    //die('ggggggggg');
    file_put_contents($cache_fs, $savejs);
  }
    
  
  return $cache_url;
}

function gks_cache_admin_pos_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-pos-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT id_acc_eidi_parastatikon_type as id, acc_eidi_parastatikon_type_descr as descr, antisimvalomenos_label as label
    FROM gks_acc_eidi_parastatikon_types
    ORDER BY id_acc_eidi_parastatikon_type;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $eidi_parastatikon_types=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $eidi_parastatikon_types[]=$row_extra;
    }
    $savejs.='var eidi_parastatikon_types=[];'."\n";
    foreach ($eidi_parastatikon_types as $value) {
      $savejs.='eidi_parastatikon_types.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\',label:\''.base64_encode($value['label']).'\'})'.";\n";
    } 
    file_put_contents($cache_fs, $savejs);
  } 
}

function gks_cache_admin_whi_mov_item() {
  global $db_link;
  global $gks_user_cache_version_prefix;
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  $cache_filename=$gks_user_cache_version_prefix.'admin-whi-mov-item.js';
  $cache_fs=$cache_dir.$cache_filename;
  if (1==2 and GKS_DEBUG and file_exists($cache_fs)) unlink($cache_fs);
  if (file_exists($cache_fs) == false) {
    $savejs='';
  
    $sql_extra="SELECT id_acc_eidi_parastatikon_type as id, acc_eidi_parastatikon_type_descr as descr, antisimvalomenos_label as label
    FROM gks_acc_eidi_parastatikon_types
    ORDER BY id_acc_eidi_parastatikon_type;";
    $result_extra = $db_link->query($sql_extra);        
    if (!$result_extra) {debug_mail(false,'error sql',$sql_extra); die('sql error');}
    $eidi_parastatikon_types=array();
    while ($row_extra = $result_extra->fetch_assoc()) {
      $eidi_parastatikon_types[]=$row_extra;
    }
    $savejs.='var eidi_parastatikon_types=[];'."\n";
    foreach ($eidi_parastatikon_types as $value) {
      $savejs.='eidi_parastatikon_types.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\',label:\''.base64_encode($value['label']).'\'})'.";\n";
    } 
    
    $sql_entitytype="SELECT id_aade_entitytype as id, aade_entitytype_descr as descr
    FROM gks_aade_entitytype
    ORDER BY sortorder;";
    $result_entitytype = $db_link->query($sql_entitytype);        
    if (!$result_entitytype) {debug_mail(false,'error sql',$sql_entitytype); die('sql error');}
    $aade_entitytype=array();
    while ($row_entitytype = $result_entitytype->fetch_assoc()) {
      $aade_entitytype[]=$row_entitytype;
    }
    $savejs.='var aade_entitytype=[];'."\n";
    foreach ($aade_entitytype as $value) {
      $savejs.='aade_entitytype.push({id:'.$value['id'].',descr:\''.base64_encode($value['descr']).'\'})'.";\n";
    }  
        
    file_put_contents($cache_fs, $savejs);
  }
  
}


