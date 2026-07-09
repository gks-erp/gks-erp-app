<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$return = array('success' => false, 'message' => base64_encode('generic error'));


$mycmd='';if (isset($_POST['cmd'])) $mycmd=trim_gks(base64_decode($_POST['cmd']));

if ($mycmd=='') {
  debug_mail(false,'mycmd',$mycmd);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').'<br>'.$mycmd));
  echo json_encode($return); die();}
  

  
$perm_type='view';
if ($mycmd=='edit') $perm_type='edit';
if ($mycmd=='add') $perm_type='add';

$my_page_title=gks_lang('Εξερεύνηση αρχείων - εντολή').': '.$mycmd;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__filesexplore',$perm_type,0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

if ($mycmd=='get_folder_data') {
  $mybasefolder='';if (isset($_POST['basefolder'])) $mybasefolder=trim_gks(base64_decode($_POST['basefolder']));
  $myfolder='';if (isset($_POST['folder'])) $myfolder=trim_gks(base64_decode($_POST['folder']));

  if ($mybasefolder=='' or ($mybasefolder!='erplo' and $mybasefolder!='erpfi' and $mybasefolder!='erpul' and $mybasefolder!='erpdl' and $mybasefolder!='wodpr')) {
    debug_mail(false,'mybasefolder',$mybasefolder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').' (112) ('.$mybasefolder.')'));
    echo json_encode($return); die();}
  
  if ($myfolder=='') {
    debug_mail(false,'myfolder',$myfolder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').' (113) ('.$myfolder.')'));
    echo json_encode($return); die();}
  
  
  $base_path='';
  if ($mybasefolder=='erplo') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site';
  else if ($mybasefolder=='erpfi') $base_path=substr(GKS_FileServerShare,0,strlen(GKS_FileServerShare)-1);
  else if ($mybasefolder=='erpul') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads';
  else if ($mybasefolder=='erpdl') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install';
  else if ($mybasefolder=='wodpr') $base_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/uploads';
  
  if (file_exists($base_path)==false or is_dir($base_path)==false) {  
    debug_mail(false,'mybasefolder',$base_path);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν υπάρχει ο φάκελος').': '.$base_path));
    echo json_encode($return); die();}
  
  $this_path=$base_path.$myfolder;
  if (file_exists($this_path)==false or is_dir($this_path)==false) {
    $resss=@mkdir($this_path);
  }
  if (file_exists($this_path)==false or is_dir($this_path)==false) {
    debug_mail(false,'myfolder',$this_path);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν υπάρχει ο φάκελος') .': '.$this_path));
    echo json_encode($return); die();}
  
  
  $data_dirs=[];
  if ($myfolder=='/') $myfolder='';
  
  $parts=explode('/',$myfolder);
  //echo '<pre>'.$myfolder.'|'.print_r($parts,true);die();
  
  //breadcrumbs
  $breadcrumbs='<a href="#" class="gks_filesexplore_breadcrumb" data-relpath="/">'.gks_lang('Αρχή').'</a> / '; 
  $rel_path='';
  foreach ($parts as $value) {
    if ($value!='') {
      $rel_path.='/'.$value;
      $breadcrumbs.='<a href="#" class="gks_filesexplore_breadcrumb" data-relpath="'.$rel_path.'">'.$value.'</a> / ';
    }
  } 
  
  //dirs
  $this_path_parts=[];
  foreach ($parts as $level => $subdir) {
    $this_path_parts[]=$subdir;
    $myimplode=implode('/',$this_path_parts);
    //if ($myimplode!='' and substr($myimplode,0,1)=='/') $myimplode=substr($myimplode,1);
    $this_path_full=$base_path.$myimplode;
    //echo '<pre>scan '.$this_path_full.'</pre>';//die();
    if (file_exists($this_path_full)) {
      
      
      $rel_path=implode(DIRECTORY_SEPARATOR,$this_path_parts);
      $parent=$this_path_parts;
      array_pop($parent);
      $data_dirs[$rel_path]=array(
        'name' => $subdir,
        'path' => $this_path_full,
        'level'=> $level+0,
        'rel_path' => $rel_path,
        'parent' => implode(DIRECTORY_SEPARATOR,$parent),
      );
            
      $dirs = scandir($this_path_full,SCANDIR_SORT_ASCENDING);
      //echo '<pre>'.$this_path_full.'|'.print_r($dirs,true);die();
      foreach ($dirs as $dir) {
        if (!($dir=='.' or $dir=='..' or strtolower($dir)=='thumbnail')) {
          $dir1=$this_path_full.DIRECTORY_SEPARATOR.$dir;
          //echo '<pre>'.$dir1.'</pre>';
          if (is_dir($dir1)) {
            $rel_path=implode(DIRECTORY_SEPARATOR,$this_path_parts).DIRECTORY_SEPARATOR.$dir;
            //if (isset($data_dirs[$rel_path])) {
            //  unset($data_dirs[$rel_path]);
            //}
            $data_dirs[$rel_path]=array(
              'name' => $dir,
              'path' => $dir1,
              'level'=>$level+1,
              'rel_path' => $rel_path,
              'parent' => implode(DIRECTORY_SEPARATOR,$this_path_parts),
            );
          }
        }
        
      }
    }
  }

  //$return['message']=base64_encode($mycmd.'|'.$mybasefolder.'|'.$myfolder.'|'.$this_path);
  //$return['message']=base64_encode('<pre>'.print_r($data_dirs,true));
  //echo json_encode($return); die();
  
  
  $html_dirs='';
  $level=1;
  do {
    $cc=0;
    $level_html='';
    foreach ($data_dirs as $mydir) {
      if ($mydir['level']==$level) {
        $cc++;
        $class_selection='';
        if ($myfolder==$mydir['rel_path']) {
          $class_selection=' gks_filesexplore_dir_selected';
        }
        $item_html='<li><div class="gks_filesexplore_dir'.$class_selection.'" data-relpath="'.$mydir['rel_path'].'">'.$mydir['name'].'</div><ul><div class="gkssubdirlisthidden">'.$mydir['rel_path'].'</div></ul></li>';
        if ($level==1) {
          $html_dirs.=$item_html;
        } else {
          
          $html_dirs=str_replace('<div class="gkssubdirlisthidden">'.$mydir['parent'].'</div>', $item_html.'<div class="gkssubdirlisthidden">'.$mydir['parent'].'</div>', $html_dirs);
          
        }
      }
    } 

    
    if ($cc==0) break;
    $level++;
  } while(true);
  
  
  
  
  //files
  $data_files=[];
  $files = scandir($this_path,SCANDIR_SORT_ASCENDING);
  foreach ($files as $file) {
    if (!($file=='.' or $file=='..' or strtolower($file)=='thumbnail')) {
      $file1=$this_path_full.DIRECTORY_SEPARATOR.$file;
      //echo '<pre>'.$dir1.'</pre>';
      if (is_file($file1)) {
        
        $myfilesize=filesize($file1);
        $myfilesize=number_format($myfilesize/1024,2,',','.').' KB';
        $myfiletime=filemtime($file1);
        $myfiletime=showDate($myfiletime,'d/m/Y H:i',1);
        
        $rel_path=$myfolder.DIRECTORY_SEPARATOR.$file;
        $url_file='';
        $url_thump='';
        $img_thump='';
        $fileext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($mybasefolder=='erplo') {
          $url_file='/my/_current/_img_site'.$rel_path;
          if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
            $url_thump='/my/_current/_img_site'.$rel_path;
            $img_thump= '<div class="gks_filesexplore_filed1">'.
                          '<div class="gks_filesexplore_filed2">'.
                            '<a class="gks_filesexplore_file_lightgallery" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                              '<img src="'.$url_thump.'">'.
                            '</a>'.
                          '</div>'.
                        '</div>'.
                        '<div class="gks_filesexplore_file_names">'.$file.'</div>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
                                               
          } else {
            $img_thump='<a class="gks_filesexplore_file_name" href="'.$url_file.'" target="_blank">'.$file.'</a>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
          }          
          
        
        } else if ($mybasefolder=='erpfi') {
          $url_file='admin-get-file.php?fs=fileservers&file='.rawurlencode($rel_path);
          if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
            $url_thump=$myfolder.'/thumbnail/'.$file;
            $url_thump='admin-get-file.php?fs=fileservers&file='.rawurlencode($url_thump);
            $img_thump= '<div class="gks_filesexplore_filed1">'.
                          '<div class="gks_filesexplore_filed2">'.
                            '<a class="gks_filesexplore_file_lightgallery" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                              '<img src="'.$url_thump.'">'.
                            '</a>'.
                          '</div>'.
                        '</div>'.
                        '<div class="gks_filesexplore_file_names">'.$file.'</div>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
                                               
          } else {
            $img_thump='<a class="gks_filesexplore_file_name" href="'.$url_file.'" target="_blank">'.$file.'</a>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
          }
          
        } else if ($mybasefolder=='erpul') {
          $url_file='/my/uploads'.$rel_path;
          if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
            $url_thump='/my/uploads'.$myfolder.'/thumbnail/'.$file;
            if (file_exists($base_path.$myfolder.'/thumbnail/'.$file)==false) {
              $url_thump='/my/uploads'.$myfolder.'/'.$file;
            }
            $img_thump= '<div class="gks_filesexplore_filed1">'.
                          '<div class="gks_filesexplore_filed2">'.
                            '<a class="gks_filesexplore_file_lightgallery" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                              '<img src="'.$url_thump.'">'.
                            '</a>'.
                          '</div>'.
                        '</div>'.
                        '<div class="gks_filesexplore_file_names">'.$file.'</div>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
                                    
          } else {
            $img_thump='<a class="gks_filesexplore_file_name" href="'.$url_file.'" target="_blank">'.$file.'</a>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
          }
        } else if ($mybasefolder=='erpdl') {
          $url_file='/my/install'.$rel_path;
          if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
            $url_thump='/my/install'.$rel_path;
            $img_thump= '<div class="gks_filesexplore_filed1">'.
                          '<div class="gks_filesexplore_filed2">'.
                            '<a class="gks_filesexplore_file_lightgallery" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                              '<img src="'.$url_thump.'">'.
                            '</a>'.
                          '</div>'.
                        '</div>'.
                        '<div class="gks_filesexplore_file_names">'.$file.'</div>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
                                               
          } else {
            $img_thump='<a class="gks_filesexplore_file_name" href="'.$url_file.'" target="_blank">'.$file.'</a>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
          }
      
        } else if ($mybasefolder=='wodpr') {
          $url_file='/wp-content/uploads'.$rel_path;
          if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
            $url_thump='/wp-content/uploads'.$rel_path;
            $img_thump= '<div class="gks_filesexplore_filed1">'.
                          '<div class="gks_filesexplore_filed2">'.
                            '<a class="gks_filesexplore_file_lightgallery" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                              '<img src="'.$url_thump.'">'.
                            '</a>'.
                          '</div>'.
                        '</div>'.
                        '<div class="gks_filesexplore_file_names">'.$file.'</div>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
                       
                       
          } else {
            $img_thump='<a class="gks_filesexplore_file_name" href="'.$url_file.'" target="_blank">'.$file.'</a>'.
                        '<div class="gks_filesexplore_file_size">'.$myfilesize.'</div>'.
                        '<div class="gks_filesexplore_file_date">'.$myfiletime.'</div>';
          }
        }        
        
        
        $data_files[]=array(
          'name' => $file,
          //'path' => $file1,
          'rel_path' =>$rel_path,
          'url_file' => $url_file,
          'url_thump' => $url_thump,
          'img_thump' => $img_thump,
        );
      }
    }
  }  

  
  //$return['message']=base64_encode('<pre>'.print_r($data_files,true));
  //echo json_encode($return); die();

  $chunck_file='filesexplore_'.date('Y_m_d_H_i_s').'_'.rand(1000,9999).rand(1000,9999);
  $html_files_first='';$html_files='';$cc=0;$chunck=1;
  foreach ($data_files as $myfile) {
    $cc++;
    $inner_html='';
    $inner_html=$myfile['img_thump'];
    
    
    $html_files.='<div class="gks_filesexplore_file newentry" data-rel_path="'.substr($myfile['rel_path'],1).'">'.$myfile['img_thump'].'</div>';
    
    if ($cc>=100) {
      if ($chunck==1) {
        $html_files_first=$html_files;
      } else {
        $savefile=GKS_SITE_PATH.'tmp/'.$chunck_file.'_'.$chunck.'.html';
        file_put_contents($savefile,$html_files);
      }
      $html_files='';
      
      $cc=0;
      $chunck++;
    }
  } 
  
  if ($html_files!='') {
    if ($chunck==1) {
      $html_files_first=$html_files;
    } else {
      $savefile=GKS_SITE_PATH.'tmp/'.$chunck_file.'_'.$chunck.'.html';
      file_put_contents($savefile,$html_files);    
    }
  }
  if ($chunck==1) $chunck_file='';
  
  if ($html_files_first=='') $html_files_first=gks_lang('Δεν βρέθηκαν αρχεία');
   
  //count($data_dirs).' folders, '.
  if (count($data_files)==0) {
    $html_footer=gks_lang('Δεν βρέθηκαν αρχεία');
  } else if (count($data_files)==1) {
    $html_footer=gks_lang('1 Αρχείο');
  } else {
    $html_footer=count($data_files).' '.gks_lang('Αρχεία');
  }
  
  
  $return['success']=true;
  $return['message']=base64_encode('OK');
  $return['html_dirs']='<ul>'.$html_dirs.'</ul>';
  $return['breadcrumbs']=$breadcrumbs;
  $return['html_files']=$html_files_first;
  $return['files_chuncks']=$chunck;
  $return['files_chunck_file']=$chunck_file;
  $return['files_count']=count($data_files);
  $return['html_footer']=$html_footer;
  //$return['message']=base64_encode($mycmd.'|'.$mybasefolder.'|'.$myfolder.'|'.$this_path);
  //$return['message']=base64_encode('<pre>'.print_r($data_dirs,true));
  echo json_encode($return); die();
  
}

if ($mycmd=='get_folder_data_loadmore') {
  $chunck_file='';if (isset($_POST['file'])) $chunck_file=trim_gks(base64_decode($_POST['file']));
  $chunck='';if (isset($_POST['chunck'])) $chunck=intval($_POST['chunck']);
  
  if ($chunck_file=='' or $chunck<=1) {
    debug_mail(false,'file chunck',$chunck_file.' '.$chunck);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').' (114) ('.$chunck_file.' '.$chunck.')'));
    echo json_encode($return); die();}
      
  $loadfile=GKS_SITE_PATH.'tmp/'.$chunck_file.'_'.$chunck.'.html';
  if (file_exists($loadfile)==false) {
    debug_mail(false,'loadfile',$loadfile);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').' (115) ('.$loadfile.')'));
    echo json_encode($return); die();}
  
  $html_files=&file_get_contents($loadfile);
  if ($html_files=='') {
    debug_mail(false,'html_files',$html_files);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα').' (116) ('.$html_files.')'));
    echo json_encode($return); die();}
    
  unlink($loadfile);
    
  $return['success']=true;
  $return['message']=base64_encode('OK');
  $return['html_files']=$html_files;
  echo json_encode($return); die();
    
}



$return['message']=base64_encode(gks_lang('Λάθος δεδομένα').' (333) ('.$mycmd.')');
echo json_encode($return); die();
