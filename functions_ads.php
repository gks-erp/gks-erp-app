<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


//https://support.google.com/google-ads/answer/7031480
function gks_erp_app_purchase_ads_head() {
  return;
  //global $GKS_ERP_APP_PURCHASE_CODE;
  //if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
  //          $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
  //  return;    
  //}  
  //if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') return;  
  //echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5474637286002621" crossorigin="anonymous"></script>'."\n";
}

function gks_erp_app_purchase_ads_footer_script() {
?>
  <script>
  myseconds=31536000;//365*24*60*60;
  var date = new Date();
  date.setTime(date.getTime() + (myseconds*1000));
  expires = "; expires=" + date.toUTCString();
  var gks_screen_resolution_w=window.screen.width;
  var gks_screen_resolution_h=window.screen.height;
  var gks_screen_resolution_cb=gks_screen_resolution_w + 'x' +gks_screen_resolution_h;
  document.cookie = "gks_screen_resolution=" + gks_screen_resolution_cb + expires + "; path=" + '/';
  
  
  </script>
<?php  

  //global $GKS_ERP_APP_PURCHASE_CODE;
  //if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
  //          $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
  //  return;    
  //}

}

$gks_screen_resolution='';
$gks_screen_resolution_w=-1;
function gks_screen_resolution_w_get() {
  global $gks_screen_resolution;
  global $gks_screen_resolution_w;
  global $gks_user_settings;
  
  if ($gks_screen_resolution_w>=0) return;
  
  $gks_screen_resolution_w=0;
  if(isset($_COOKIE['gks_screen_resolution'])) $gks_screen_resolution=trim_gks($_COOKIE['gks_screen_resolution']);
  if ($gks_screen_resolution!='') {
    $parts=explode('x',$gks_screen_resolution);
    if (count($parts)==2) $gks_screen_resolution_w=intval($parts[0]);
    if ($gks_screen_resolution_w>=992 and
        isset($gks_user_settings['menu']['pos']) and
        $gks_user_settings['menu']['pos']=='left') {
      $gks_screen_resolution_w-=160;
    }
  } 
  if ($gks_screen_resolution_w==0) {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $useragent=$_SERVER['HTTP_USER_AGENT'];
      if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
        $gks_screen_resolution_w=576;
      } else {
        $gks_screen_resolution_w=1920;
      }
    }
  }
  //echo $gks_screen_resolution.'|'.$gks_screen_resolution_w.'|';
    
}

/*
'afterfilters'
'item'
'pivot_a'
'pivot_b'
'page'
*/

function gks_erp_app_purchase_ads_fix_970x90($location='',$myclass='gks_ads_div_inpage') {
  if ($location=='afterfilters') return;
  if ($location=='pivot_a') return;
  
  
  global $gks_screen_resolution;
  global $gks_screen_resolution_w;
  global $GKS_ERP_APP_PURCHASE_CODE;
  
  gks_screen_resolution_w_get();
  if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
            $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
    return;    
  }
  
  if ($gks_screen_resolution_w>=970) {
    $mysize='width:970px;height:90px;';$urlsize='970x90';
    //$myslot='4055802607';
  } else if ($gks_screen_resolution_w>=728) {
    $mysize='width:728px;height:90px;';$urlsize='728x90';
    //$myslot='4244688066';
  } else if ($gks_screen_resolution_w>=468) {
    $mysize='width:468px;height:60px;';$urlsize='468x60';
    //$myslot='9067105069';
  //} else if ($gks_screen_resolution_w>=336) {
  //  $mysize='width:336px;height:280px;';
  //  $myslot='4838201544';
  } else if ($gks_screen_resolution_w>=300) {
    $mysize='width:300px;height:100px;';$urlsize='300x100';
    //$myslot='1525642496';
  } else if ($gks_screen_resolution_w>=250) {
    $mysize='width:250px;height:250px;';$urlsize='250x250';
    //$myslot='8739802007';
  } else { 
    $mysize='width:200px;height:200px;';$urlsize='200x200';
    //$myslot='4053116373';
  }
  
//  if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') {
//    echo '<div class="'.$myclass.'">'.
//      '<ins class="adsbygoogle_place" style="display:inline-block;'.$mysize.'"><div class="gks_ads_div_test_full"></div></ins>'.
//    '</div>';
//    return;
//  }
  
  echo 
  '<div class="'.$myclass.'">'.
    '<iframe scrolling="no" '.
     'src="https://ads.gks.gr/ad/?v='.time().'&l='.$location.'&c='.$myclass.'&s='.$urlsize.'&r='.$gks_screen_resolution_w.'" '.
     'style="display:inline-block;border: 0px;'.$mysize.'" '.
     'title="gks ERP Ad">'.
    '</iframe>'.
  '</div>';
   
//echo '<div class="'.$myclass.'"><ins class="adsbygoogle" 
//style="display:inline-block;'.$mysize.'"
//data-ad-client="ca-pub-5474637286002621"
//data-ad-slot="'.$myslot.'"></ins></div>'.
//'<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';

}

function gks_erp_app_purchase_ads_fix_item_card() {
  return;
  global $gks_screen_resolution;
  global $gks_screen_resolution_w;
  global $GKS_ERP_APP_PURCHASE_CODE;
  
  gks_screen_resolution_w_get();
  if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
            $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
    return;    
  }
  if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') {
    $mysize='width:100%;height:200px;';
    echo '<div class="gks_ads_div_item_card">'.
      '<ins class="adsbygoogle_place" style="display:inline-block;'.$mysize.'"><div class="gks_ads_div_test_full"></div></ins>'.
    '</div>';
    return;
  }

echo '<div class="gks_ads_div_item_card"><ins class="adsbygoogle"
style="display:block"
data-ad-client="ca-pub-5474637286002621"
data-ad-slot="9236611553"
data-ad-format="auto"
data-full-width-responsive="true"></ins></div>'.
'<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';

     
}

function gks_erp_app_purchase_ads_header_ad() {
  
  gks_erp_app_purchase_ads_fix_970x90('header','gks_ads_div_header');
  return;
  /*
  global $gks_screen_resolution;
  global $gks_screen_resolution_w;
  global $GKS_ERP_APP_PURCHASE_CODE;
  
  gks_screen_resolution_w_get();
  if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
            $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
    return;    
  }
  if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') {
    $mysize='width:100%;height:200px;';
    echo '<div class="gks_ads_div_header">'.
      '<ins class="adsbygoogle_place" style="display:inline-block;'.$mysize.'"><div class="gks_ads_div_test_full"></div></ins>'.
    '</div>';
    return;
  }



echo '<div class="gks_ads_div_header"><ins class="adsbygoogle"
style="display:block"
data-ad-client="ca-pub-5474637286002621"
data-ad-slot="2653739957"
data-ad-format="auto"
data-full-width-responsive="true"></ins></div>'.
'<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
*/

}

function gks_erp_app_purchase_ads_footer_ad() {
  global $gks_screen_resolution;
  global $gks_screen_resolution_w;
  global $GKS_ERP_APP_PURCHASE_CODE;
  
  gks_screen_resolution_w_get();
  if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']) and
            $GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']['valid']==true) {
    return;    
  }
  
  $location='footer';
  $myclass='gks_ads_div_footer';
  $urlsize='full';
  
  echo 
  '<div class="'.$myclass.'">'.
    '<iframe scrolling="no" '.
     'src="https://ads.gks.gr/ad/?v='.time().'&l='.$location.'&c='.$myclass.'&s='.$urlsize.'&r='.$gks_screen_resolution_w.'" '.
     'style="display:inline-block;border: 0px;width:100%;height:350px;" '.
     'title="gks ERP Ad">'.
    '</iframe>'.
  '</div>';
    
  return;
  
//  if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') {
//    $mysize='width:100%;height:200px;';
//    echo '<div class="gks_ads_div_footer">'.
//      '<ins class="adsbygoogle_place" style="display:inline-block;'.$mysize.'"><div class="gks_ads_div_test_full"></div></ins>'.
//    '</div>';
//    return;
//  }



//echo '<div class="gks_ads_div_footer"><ins class="adsbygoogle"
//style="display:block"
//data-ad-format="autorelaxed"
//data-ad-client="ca-pub-5474637286002621"
//data-ad-slot="3957747604"></ins></div>'.
//'<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';

}
