<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_customtableview_get_user_settings($user_id=0,$page='') {
  global $db_link;
  global $my_wp_user_id;
  
  $ret=array();
  $ret['cookie']=[];
  $ret['class']=[];
  $ret['data']=[];
  

  if ($user_id==0 and $my_wp_user_id>0) $user_id=$my_wp_user_id;
  if ($user_id==0) return $ret;
  if ($page=='') {
    $tmp='';
    if (isset($_SERVER['SCRIPT_NAME'])) $tmp=trim_gks($_SERVER['SCRIPT_NAME']);
    $parts=explode('/',$tmp);
    if (count($parts)==3 and $parts[0]=='' and $parts[1]=='my' and $parts[2]!='') {
      $page=trim_gks($parts[2]);
    }
  }
  if ($page=='') return $ret;

  //echo '<pre>';print_r($page);die();
  
  $data=array();
  $sql="select myvalue from gks_settings_users 
  where user_id=".$user_id."
  and myobject='gks_customtableview'
  and mysubobject='".$db_link->escape_string($page)."'";
  //echo $sql;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $value=$row['myvalue'];
    

    $data = unserialize($value);
    if (is_array($data)==false) {
      debug_mail(false,'json_decode error gks_customtableview_get_user_settings',$value);
      $data=array();
      //as sinexisei, den einai simantiko
    }
    
  }
  $ret['data']=$data;

  
  
  if (isset($_COOKIE['gks_customtableview'])) {
    //echo '<pre>';print_r($_COOKIE['gks_customtableview']);die();
    $mycookie=$_COOKIE['gks_customtableview'];
    $mycookie=str_replace('\"', '"', $mycookie);
    $mycookie=json_decode($mycookie, true);
    //echo '<pre>';var_dump($mycookie);die();
    if (!($mycookie === null && json_last_error() !== JSON_ERROR_NONE)) {
      $ret['cookie']=$mycookie;
    }  
  }

  if (isset($ret['cookie']['tables'])) {
    foreach ($ret['cookie']['tables'] as &$mytable) {
      $mytable['view_is_ok']=false;
      $ret['class'][$mytable['tableindex']]='';
      foreach ($ret['data'] as $datatable) {
        if ($datatable['tableindex']==$mytable['tableindex']) {
          foreach ($datatable['views'] as $dataview) {
            if ($mytable['viewindex']==$dataview['vindex']) {
              $mytable['view_is_ok']=true;
              $ret['class'][$mytable['tableindex']]='gkstable_gks_customtableview';
              break;
            }
          }
        }
      }
    }
    unset($mytable);
  }

  
  
  return $ret;
  
/*
  tableindex 
    viewindex
      data


*/
}

function gks_customtableview_render_css(&$input_data,$index) {
  if (isset($input_data['class'][$index])==false) {
    $input_data['class'][$index]='';
  }

  $view_selected=0;
  if (isset($input_data['cookie']['tables'][$index]['viewindex']) and
            $input_data['cookie']['tables'][$index]['view_is_ok']==true) {
    $view_selected=$input_data['cookie']['tables'][$index]['viewindex'];
  }
  //echo $view_selected;die();   
  if ($view_selected==0) return '';

  $mylist=[];
  if (isset($input_data['data'][$index]['views'][$view_selected]['mylist'])) {
    $mylist=$input_data['data'][$index]['views'][$view_selected]['mylist'];
  }
  if (count($mylist)==0) return '';
  $selector=$input_data['data'][$index]['selector'];
  
  //print '<pre>';print_r($input_data);die();
  //print '<pre>';print_r($mylist);die();
  
  $mycss='';
  foreach ($mylist as $value) {
    $item=':nth-child(' . $value['i'] . ') {' .
      'width:' . $value['pix'] . 'px;';
    if ($value['vis']==false) $item.='display:none;';
    
    $flex='';
    if ($value['exp']) $flex.='1 '; else $flex.='0 ';
    if ($value['com']) $flex.='1 '; else $flex.='0 ';
    $flex.='auto';
    $item.='flex:' .  $flex . ';';
    $item.='order:' .  $value['myo'] . ';';
    $item.='} ' . "\n";
    
    $mycss.=$selector . $item;
    if ($selector=='.h_tra_rsrv_rr > div') $mycss.='.tra_rsrv_rr > div' . $item;
    if ($selector=='.h_tra_rsrv_ro > div') $mycss.='.tra_rsrv_ro > div' . $item;
    if ($selector=='.gkstable > thead > tr > th') {
      $mycss.='.gkstable > tbody > tr > th' . $item;
      $mycss.='.gkstable > tbody > tr > td' . $item;
    }
    if ($selector=='.gkssubtable > thead > tr > th') {
      $mycss.='.gkssubtable > tbody > tr > th' . $item;
      $mycss.='.gkssubtable > tbody > tr > td' . $item;
    }
    
  }
  return $mycss;

  //gkstable_gks_customtableview  
}

function gks_customtableview_php_generate($input_data,$index=1,$selector='.gkstable > thead > tr > th',$title='',$class='') {
  if ($title=='') $title=gks_lang('Ρύθμιση προβολής');
  
  $views=[];
  if (isset($input_data['data'][$index]['views']) and 
      is_array($input_data['data'][$index]['views'])) {
    $views=$input_data['data'][$index]['views'];
  }
  
  $view_selected=0;
  if (isset($input_data['cookie']['tables'][$index]['viewindex']) and
            $input_data['cookie']['tables'][$index]['view_is_ok']==true) {
    $view_selected=$input_data['cookie']['tables'][$index]['viewindex'];
  }
  
  $html=
  '<div class="gks_customtableview_btn_group btn-group" 
    data-selector="'.$selector.'" 
    data-index="'.$index.'">
    <button class="gks_customtableview_btn_button btn btn-primary dropdown-toggle tooltipster '.$class.'" type="button" 
      title="'.$title.'"
      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
      ><i class="fas fa-sliders-h"></i></button>
    <div class="gks_customtableview_btn_items dropdown-menu dropdown-menu-right">
      <div class="gks_customtableview_btn_item dropdown-item '.
      ((count($views)==0 or $view_selected==0) ? 'active' : '').
      '" data-is_default="1" style="'.
      ((count($views)==0) ? 'display:none;' : '').
      '">'.gks_lang('Προεπιλογή').'</div>
      <div class="gks_customtableview_btn_item dropdown-item" data-is_new="1"><span>'.gks_lang('Νέα Προβολή').'</span></div>';

      foreach ($views as $dataview) {
        $class_active='';
        if ($view_selected==$dataview['vindex']) $class_active='active';
        $html.='<div class="gks_customtableview_btn_item dropdown-item '.$class_active.'" data-vindex="' . $dataview['vindex'] . '"><span title="'.gks_lang('Εφαρμογή').'">' . $dataview['name'] . '</span> <i class="fas fa-pen" title="'.gks_lang('Επεξεργασία').'"></i></div>';
      }
  
  $html.='
    </div>
  </div>';
  
  return $html;  
}






