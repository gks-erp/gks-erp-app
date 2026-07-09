<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Εκτέλεση ερωτήματος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__sql','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql = trim_gks(base64_decode($_POST['query']));
if ($sql=='') {
  debug_mail(false,'set query','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε κάποιο ερώτημα')));
  echo json_encode($return); die();}

//echo '<pre>';echo  $sql;die(); 

$sql_log="insert into gks_sql_log (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
user_id,`sql`
) values (
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
".$my_wp_user_id.",'".$db_link->escape_string($sql)."'
);";
$result_log = $db_link->query($sql_log);  
if (!$result_log) {
  debug_mail(false,'error sql',$sql_log);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$id_sql_log = $db_link->insert_id; 
$log_res=0;
$log_rows=0;



$sql = preg_replace('/--[^\n]*\n?/', '', $sql);       // single line comments
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);      // multi-line comments
$sql = trim($sql);

// take 1st word
$keyword = strtoupper(strtok($sql, " \t\n\r"));
$mytype='';
switch($keyword) {
    case 'SELECT':
    case 'SHOW':
    case 'DESCRIBE':
    case 'DESC':
    case 'EXPLAIN':
      $mytype='SELECT';break;
    case 'INSERT':    $mytype='INSERT';break;
    case 'UPDATE':    $mytype='UPDATE';break;
    case 'DELETE':    $mytype='DELETE';break;
    case 'CREATE':    $mytype='CREATE';break;
    case 'DROP':      $mytype='DROP';break;
    case 'ALTER':     $mytype='ALTER';break;
    case 'TRUNCATE':  $mytype='TRUNCATE';break;
};
if ($mytype=='') {
  debug_mail(false,'query no mytype','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν ανιχνεύτηκε ο τύπος του ερωτήματος')));
  echo json_encode($return); die();}

$results=''; 
$result = $db_link->query($sql);        
if (!$result) {
  $return = array('success' => false, 'message' => base64_encode('sql error: '.$db_link->error),'results'=>$results);
  echo json_encode($return); die();}

if ($mytype=='SELECT') {
  $results='<table class="table table-sm table-responsive11 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">'.
  '<thead>'.
  '<tr>'.
  '<th nowrap id="gks_th_aa" class="table-dark" scope="col" style="text-align: center" nowrap width="0%">#</th>';
  
  $field_info = $result->fetch_fields();
  //echo '<pre>';var_dump($field_info);die();
  $field_names=[];
  foreach($field_info as $fi) {
    $results.='<th nowrap class="table-dark " scope="col" style="text-align: left" nowrap width="0%">'.$fi->name.'</th>';
    $field_names[]=[$fi->name,$fi->type];
    
  }
  $results.='</tr></<thead><tbody>';
  $i=0;
  //echo '<pre>';print_r($field_names);die();
  $bottom_text='';
  while ($row = $result->fetch_assoc()) {
    $i++;
    $results.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">';
    $results.='<td scope="row" nowrap class="mytdcm">'.$i.'</td>';
    
    for($j=0; $j<count($field_names);$j++) {
      
      switch ($field_names[$j][1])
      {
        case MYSQLI_TYPE_DECIMAL:
        case MYSQLI_TYPE_NEWDECIMAL:
        case MYSQLI_TYPE_FLOAT:
        case MYSQLI_TYPE_DOUBLE:
            $results.='<td class="mytdcml sql_type_'.$field_names[$j][1].'" nowrap>'.htmlspecialchars($row[$field_names[$j][0]]).'</td>';
            break;

        case MYSQLI_TYPE_BIT:
        case MYSQLI_TYPE_TINY:
        case MYSQLI_TYPE_SHORT:
        case MYSQLI_TYPE_LONG:
        case MYSQLI_TYPE_LONGLONG:
        case MYSQLI_TYPE_INT24:
        case MYSQLI_TYPE_YEAR:
        case MYSQLI_TYPE_ENUM:
            $results.='<td class="mytdcml sql_type_'.$field_names[$j][1].'" nowrap>'.htmlspecialchars($row[$field_names[$j][0]]).'</td>';
            break;
            
        case MYSQLI_TYPE_TIMESTAMP:
        case MYSQLI_TYPE_DATE:
        case MYSQLI_TYPE_TIME:
        case MYSQLI_TYPE_DATETIME:
            $results.='<td class="mytdcml sql_type_'.$field_names[$j][1].'" nowrap>'.htmlspecialchars($row[$field_names[$j][0]]).'</td>';
            break;
        
        case MYSQLI_TYPE_NEWDATE:
        case MYSQLI_TYPE_INTERVAL:
        case MYSQLI_TYPE_SET:
        case MYSQLI_TYPE_VAR_STRING:
        case MYSQLI_TYPE_STRING:
        case MYSQLI_TYPE_CHAR:
        case MYSQLI_TYPE_GEOMETRY:
            $temp=$row[$field_names[$j][0]];
            if (strlen($temp)>50) {
              $results.='<td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.
              htmlspecialchars($row[$field_names[$j][0]]).
              '</div></div></td>';
            } else {
              $results.='<td class="mytdcml sql_type_'.$field_names[$j][1].'" nowrap>'.htmlspecialchars($temp).'</td>';
            }
            break;
        case MYSQLI_TYPE_TINY_BLOB:
        case MYSQLI_TYPE_MEDIUM_BLOB:
        case MYSQLI_TYPE_LONG_BLOB:
        case MYSQLI_TYPE_BLOB:
            $results.='<td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.
            htmlspecialchars($row[$field_names[$j][0]]).
            '</div></div></td>'; 
            break;

        default:
            $results.='<td class="mytdcml sql_type_'.$field_names[$j][1].'" type_is_agnosto>'.htmlspecialchars($row[$field_names[$j][0]]).'</td>';
            break;
      }

    }
    $results.='</tr>';
    if ($i>=1000 && $result->num_rows>1000) {
      $bottom_text=gks_lang('Εμφανίζονται οι πρώτες 1000 εγγραφές').
      '<br>'.str_replace('[1]',$result->num_rows,gks_lang('Το ερώτημα επέστρεψε [1] εγγραφές'));
      break;
    }
  }
  
  $results.='</tbody></table>';
  $log_res=1;
  $log_rows=$result->num_rows;

  if ($bottom_text!='') {
    $results.='<div class="alert alert-danger" role="alert">'.$bottom_text.'</div>';
  }
} else {
  $log_res=1;
  $log_rows=$db_link->affected_rows;
  $results='Affected Rows: '.$log_rows;
  
}

$sql_log="update gks_sql_log set 
`res`=".$log_res.",
`rows`=".$log_rows."
where id_sql_log=".$id_sql_log;
$result_log = $db_link->query($sql_log);  
if (!$result_log) {
  debug_mail(false,'error sql',$sql_log);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$return = array('success' => true, 'message' => base64_encode('OK'),'results'=>$results, 'mytype'=>$mytype);
echo json_encode($return); die();
