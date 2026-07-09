<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
https://github.com/PHPOffice/PhpSpreadsheet/blob/master/CHANGELOG.md
Worksheet methods that reference cells "byColumnandRow". All such methods have an equivalent that references the cell by its address (e.g. 'E3' rather than 5, 3).

These functions now accept either a cell address string ('E3') or an array with columnId and rowId ([5, 3]) or a new CellAddress object as their cellAddress/coordinate argument. This includes the methods:

setCellValueByColumnAndRow() use the equivalent setCellValue()
setCellValueExplicitByColumnAndRow() use the equivalent setCellValueExplicit()
getCellByColumnAndRow() use the equivalent getCell()
cellExistsByColumnAndRow() use the equivalent cellExists()
getStyleByColumnAndRow() use the equivalent getStyle()
setBreakByColumnAndRow() use the equivalent setBreak()
mergeCellsByColumnAndRow() use the equivalent mergeCells()
unmergeCellsByColumnAndRow() use the equivalent unmergeCells()
protectCellsByColumnAndRow() use the equivalent protectCells()
unprotectCellsByColumnAndRow() use the equivalent unprotectCells()
setAutoFilterByColumnAndRow() use the equivalent setAutoFilter()
freezePaneByColumnAndRow() use the equivalent freezePane()
getCommentByColumnAndRow() use the equivalent getComment()
setSelectedCellByColumnAndRow() use the equivalent setSelectedCells()
*/
function gks_export_excel_get_a(&$myarray) {
  foreach ($myarray as $i => $field) {
    preg_match_all("~\[((?:[^\[\]]++|(?R))*)\]~", $field['f'], $params);
    if (is_array($params) and isset($params[0])) {
      $myarray[$i]['a']=$params[0];
    }
  }  
}
function gks_export_excel_get_ftypes(&$myarray, $result) {
  //https://stackoverflow.com/questions/5824722/mysqli-how-to-get-the-type-of-a-column-in-a-table

  $myarray=array();
  if ($result->num_rows>=1) {
    while ($column_info = $result->fetch_field()) {
      $mtype='';
      switch ($column_info->type) {   
   
        case 1: $mtype='TINYINT'; break;
        case 2: $mtype='SMALLINT'; break;
        case 3: $mtype='INTEGER'; break;
        case 4: $mtype='FLOAT'; break;
        case 5: $mtype='DOUBLE'; break;
        case 7: $mtype='TIMESTAMP'; break;
        case 8: $mtype='BIGINT'; break;
        case 9: $mtype='MEDIUMINT'; break;
        case 10: $mtype='DATE'; break;
        case 11: $mtype='TIME'; break;
        case 12: $mtype='DATETIME'; break;
        case 13: $mtype='YEAR'; break;
        case 16: $mtype='BIT'; break;
        case 246: $mtype='DECIMAL'; break;
        case 252: $mtype='TEXT'; break;
        case 253: $mtype='VARCHAR'; break; 
        case 254: $mtype='CHAR'; break;
        default: 
          $error_text='agnostos typos field (1) '.$column_info->type.' '.$column_info->name;
          if (in_array($error_text,$errors)==false) $errors[]=$error_text;
          break;
        
      }
      $myarray[$column_info->name]=$mtype;
      //echo $column_info->name.'|'.$column_info->type."\n";
    }
  }

}


function gks_export_excel($export_excel_params) {
  if (PHP_SAPI == 'cli') die('This page should only be run from a Web Browser');
  	
  //error_reporting(E_ALL);
  //ini_set('display_errors', TRUE);
  //ini_set('display_startup_errors', TRUE);
  //date_default_timezone_set('Europe/London');


  global $my_wp_user_id;
  global $db_link;
  global $GKS_SITE_HUMAN_NAME;
  $result=$export_excel_params['result'];
  $id_export_excel=$export_excel_params['id_export_excel'];
  
  if ($id_export_excel<=0) {debug_mail(false,'id_export_excel is not set',$id_export_excel);die('Error set at id_export_excel');}
  
  
  $sql_export_excel="select * from gks_export_excel where id_export_excel=".$id_export_excel;
  $result_export_excel = $db_link->query($sql_export_excel);        
  if (!$result_export_excel) {
    debug_mail(false,'error sql',$sql_export_excel);die('sql error');
  }
  if ($result_export_excel->num_rows!=1) {
    debug_mail(false,'export excel record not found',$sql_export_excel); 
    die('no record found');
  }
  $row_export_excel = $result_export_excel->fetch_assoc();
  $export_excel_object=trim_gks($row_export_excel['export_excel_object']);
  $export_excel_filename_prefix=trim_gks($row_export_excel['export_excel_filename_prefix']);
  $export_excel_start_col=intval($row_export_excel['export_excel_start_col']);
  $export_excel_start_row=intval($row_export_excel['export_excel_start_row']);
  if ($export_excel_start_col<1) $export_excel_start_col=1;
  if ($export_excel_start_row<1) $export_excel_start_row=1;
  $export_excel_start_col=$export_excel_start_col-1;
  $export_excel_start_row=$export_excel_start_row-1;
  
  $export_excel_descr=trim_gks($row_export_excel['export_excel_descr']);
  $export_excel_data=$row_export_excel['export_excel_data'];
  
  if (isset($export_excel_params['data'])) {
    $data=$export_excel_params['data'];
  } else {
    $data=array();
    if ($export_excel_data!='') $data=unserialize($export_excel_data);
    if (is_array($data)==false) die('data is empty');
    if (count($data)<=0) die('data is empty');
    //print '<pre>';print_r($data);die();
    //print '<pre>';var_dump($data);die();
  }
  
  if (isset($data['static'])==false) $data['static']=array();
  if (isset($data['main'])==false) $data['main']=array();
  if (isset($data['eidi'])==false) $data['eidi']=array();
  
  
  $row_user=array();
  if (isset($data['static']) and count($data['static']) > 0) {
    $sql_user="SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_users.*
    FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID=".$my_wp_user_id;
    $result_user = $db_link->query($sql_user);        
    if (!$result_user) {debug_mail(false,'error sql',$sql_user);die('sql error');}
    if ($result_user->num_rows>=1) {
      $row_user = $result_user->fetch_assoc();
    }
  }
  
  
  gks_export_excel_get_a($data['main']);
  gks_export_excel_get_a($data['eidi']);
  
  //print '<pre>'; print_r($data); die();
  
  
  //use PhpOffice\PhpSpreadsheet\Spreadsheet;
  //use PhpOffice\PhpSpreadsheet\Cell\DataType;
  //use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
  //use PhpOffice\PhpSpreadsheet\Style\Border;
  //use PhpOffice\PhpSpreadsheet\Style\Fill;
  //use PhpOffice\PhpSpreadsheet\Style\Style;
  //
  //use PhpOffice\PhpSpreadsheet\RichText\RichText;
  //use PhpOffice\PhpSpreadsheet\Shared\Date;
  //use PhpOffice\PhpSpreadsheet\Style\Alignment;
  //use PhpOffice\PhpSpreadsheet\Style\Color;
  //use PhpOffice\PhpSpreadsheet\Style\Font;
  //use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
  //use PhpOffice\PhpSpreadsheet\Style\Protection;
  //use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
  //use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
  
  $errors=array();
  
  //Cell::setValueBinder(new AdvancedValueBinder());
  
  $objPHPExcel = new PhpOffice\PhpSpreadsheet\Spreadsheet();
  
  // Set document properties
  $objPHPExcel->getProperties()->setCreator($GKS_SITE_HUMAN_NAME)
  							 ->setLastModifiedBy($GKS_SITE_HUMAN_NAME)
  							 ->setTitle($export_excel_descr)
  							 ->setSubject($export_excel_descr)
  							 ->setDescription($export_excel_descr)
  							 ->setKeywords($export_excel_descr.' '.$GKS_SITE_HUMAN_NAME)
  							 ->setCategory($export_excel_descr.' '.$GKS_SITE_HUMAN_NAME);
  
  $mystyle= [
              'fill' => [
                'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '00aaaaaa'],
              ],
              //'borders' => [
              //  'bottom' => ['borderStyle' => Border::BORDER_THIN],
              //  'right' => ['borderStyle' => Border::BORDER_MEDIUM],
              //],
            ];
  
  $mysheet = $objPHPExcel->getActiveSheet();
  
  if (isset($data['static']) and count($data['static']) > 0) {
    foreach ($data['static'] as $i => $field) {
      //print '<pre>';print_r($field);die();
      switch ($field['v']) {   
        case 'now': 
          $mysheet->setCellValue($field['c'], PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(_time_user(time(),1)));
          $mysheet->getCell($field['c'])->getStyle()->getNumberFormat()->setFormatCode('dd/mm/yyyy h:mm');
          break;  
        case 'date': 
          $mysheet->setCellValue($field['c'], PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(_time_user(time(),1)));
          $mysheet->getCell($field['c'])->getStyle()->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
          
          //$mysheet->getCell($field['c'])->setValueExplicit(trim_gks($field['v']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'user_login': 
          if (isset($row_user['user_login'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['user_login']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'user_email': 
          if (isset($row_user['user_email'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['user_email']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'user_display_name': 
          if (isset($row_user['display_name'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['display_name']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'gks_nickname': 
          if (isset($row_user['gks_nickname'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['gks_nickname']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'gks_fullname': 
          if (isset($row_user['gks_fullname'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['gks_fullname']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        case 'gks_mobile': 
          if (isset($row_user['gks_mobile'])) 
            $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($row_user['gks_mobile']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;  
        default: 
          $mysheet->getCell($field['c'])->setValueExplicit(trim_gks($field['v']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          break;
      }
      
    }
  }
  
  
  
  
  foreach ($data['main'] as $i => $field) {
    $ic= $export_excel_start_col + $i + 1;
    //$mysheet->getCell([$ic,$export_excel_start_row+1])->setValueExplicit(trim_gks($field['h']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle);
    $mysheet->getCellByColumnAndRow($ic,$export_excel_start_row+1)->setValueExplicit(trim_gks($field['h']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle);
  }
  
  $eidi_col_start=$export_excel_start_col + count($data['main']);
  if (isset($data['eidi']) and count($data['eidi']) > 0) {
    foreach ($data['eidi'] as $i => $field) {
      $ic= $eidi_col_start + $i + 1;
      //$mysheet->getCell([$ic,$export_excel_start_row+1])->setValueExplicit(trim_gks($field['h']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle);
      $mysheet->getCellByColumnAndRow($ic,$export_excel_start_row+1)->setValueExplicit(trim_gks($field['h']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle);
    }
  }
  
  
  gks_export_excel_get_ftypes($data['ftypes']['main'],$result);
  //echo '<pre>';print_r($data);die();
  //echo '<pre>';print_r($data['ftypes']);die();
  
  
  
  $rows=array();
  $id_order_ids=array();
  $delivery_id_8_ids=array();
  $order_type_ids=array();
  $book_type_ids=array();
  $printing_type_ids=array();
  $present_type_ids=array();
  $box_type_ids=array();
  $subtype_ids=array();
  $cover_type_ids=array();
  $book_size_ids=array();
  $album_size_ids=array();
  $present_size_ids=array();
  $box_size_ids=array();
  $box_color_ids=array();
  $coating_ids=array();
  $print_paper_ids=array();
  $paper_size_ids=array();
  $present_color_ids=array();
  $pass_color_ids=array();
  $printype_ids=array();
  $foiltype_ids=array();
  $cover_code_ids=array();
  $coll_design_ids=array();
  $pack_type_ids=array();
  $thickness_ids=array();
  $lamination_ids=array();
  $mounting_ids=array();
  $telaro_ids=array();
  $frame_ids=array();
  $fcolor_ids=array();
  $ppcolor_ids=array();
  $sub_type_ids=array();
  $subalbum_size_ids=array();
  $subpaper_type_ids=array();
  $subcover_code_ids=array();
  $subcoll_design_ids=array();
  
  
  $id_acc_inv_ids=array();
  
  while ($row = $result->fetch_assoc()) {
    
    switch ($export_excel_object) {
      case 'wp_users':
        
        break;
      case 'gks_orders':
        $row['eidi']=array();
        $row['products_count']=0; $data['ftypes']['main']['products_count']='INTEGER';
        $row['products_sets']=0; $data['ftypes']['main']['products_sets']='INTEGER';
        
        $id_order_ids[]=$row['id_order'];
        if (isset($row['delivery_id_8']) and $row['delivery_id_8']!=0 and in_array($row['delivery_id_8'], $delivery_id_8_ids)==false) 
          $delivery_id_8_ids[]=$row['delivery_id_8'];
        if (isset($row['order_type']) and $row['order_type']!=0 and in_array($row['order_type'], $order_type_ids)==false) 
          $order_type_ids[]=$row['order_type'];
        if (isset($row['book_type']) and $row['book_type']!=0 and in_array($row['book_type'], $book_type_ids)==false) 
          $book_type_ids[]=$row['book_type'];
        if (isset($row['printing_type']) and $row['printing_type']!=0 and in_array($row['printing_type'], $printing_type_ids)==false) 
          $printing_type_ids[]=$row['printing_type'];
        if (isset($row['present_type']) and $row['present_type']!=0 and in_array($row['present_type'], $present_type_ids)==false) 
          $present_type_ids[]=$row['present_type'];
        if (isset($row['box_type']) and $row['box_type']!=0 and in_array($row['box_type'], $box_type_ids)==false) 
          $box_type_ids[]=$row['box_type'];
        if (isset($row['subtype']) and $row['subtype']!=0 and in_array($row['subtype'], $subtype_ids)==false) 
          $subtype_ids[]=$row['subtype'];
        if (isset($row['cover_type']) and $row['cover_type']!=0 and in_array($row['cover_type'], $cover_type_ids)==false) 
          $cover_type_ids[]=$row['cover_type'];
        if (isset($row['book_size']) and $row['book_size']!=0 and in_array($row['book_size'], $book_size_ids)==false) 
          $book_size_ids[]=$row['book_size'];
        if (isset($row['album_size']) and $row['album_size']!=0 and in_array($row['album_size'], $album_size_ids)==false) 
          $album_size_ids[]=$row['album_size'];
        if (isset($row['present_size']) and $row['present_size']!=0 and in_array($row['present_size'], $present_size_ids)==false) 
          $present_size_ids[]=$row['present_size'];
        if (isset($row['box_size']) and $row['box_size']!=0 and in_array($row['box_size'], $box_size_ids)==false) 
          $box_size_ids[]=$row['box_size'];
        if (isset($row['box_color']) and $row['box_color']!=0 and in_array($row['box_color'], $box_color_ids)==false) 
          $box_color_ids[]=$row['box_color'];
        if (isset($row['coating']) and $row['coating']!=0 and in_array($row['coating'], $coating_ids)==false) 
          $coating_ids[]=$row['coating'];
        if (isset($row['print_paper']) and $row['print_paper']!=0 and in_array($row['print_paper'], $print_paper_ids)==false) 
          $print_paper_ids[]=$row['print_paper'];
        if (isset($row['paper_size']) and $row['paper_size']!=0 and in_array($row['paper_size'], $paper_size_ids)==false) 
          $paper_size_ids[]=$row['paper_size'];
        if (isset($row['present_color']) and $row['present_color']!=0 and in_array($row['present_color'], $present_color_ids)==false) 
          $present_color_ids[]=$row['present_color'];
        if (isset($row['pass_color']) and $row['pass_color']!=0 and in_array($row['pass_color'], $pass_color_ids)==false) 
          $pass_color_ids[]=$row['pass_color'];
        if (isset($row['printype']) and $row['printype']!=0 and in_array($row['printype'], $printype_ids)==false) 
          $printype_ids[]=$row['printype'];
        if (isset($row['foiltype']) and $row['foiltype']!=0 and in_array($row['foiltype'], $foiltype_ids)==false) 
          $foiltype_ids[]=$row['foiltype'];
        if (isset($row['cover_code']) and $row['cover_code']!=0 and in_array($row['cover_code'], $cover_code_ids)==false) 
          $cover_code_ids[]=$row['cover_code'];
        if (isset($row['coll_design']) and $row['coll_design']!=0 and in_array($row['coll_design'], $coll_design_ids)==false) 
          $coll_design_ids[]=$row['coll_design'];
        if (isset($row['pack_type']) and $row['pack_type']!=0 and in_array($row['pack_type'], $pack_type_ids)==false) 
          $pack_type_ids[]=$row['pack_type'];
        if (isset($row['thickness']) and $row['thickness']!=0 and in_array($row['thickness'], $thickness_ids)==false) 
          $thickness_ids[]=$row['thickness'];
        if (isset($row['lamination']) and $row['lamination']!=0 and in_array($row['lamination'], $lamination_ids)==false) 
          $lamination_ids[]=$row['lamination'];
        if (isset($row['mounting']) and $row['mounting']!=0 and in_array($row['mounting'], $mounting_ids)==false) 
          $mounting_ids[]=$row['mounting'];
        if (isset($row['telaro']) and $row['telaro']!=0 and in_array($row['telaro'], $telaro_ids)==false) 
          $telaro_ids[]=$row['telaro'];
        if (isset($row['frame']) and $row['frame']!=0 and in_array($row['frame'], $frame_ids)==false) 
          $frame_ids[]=$row['frame'];
        if (isset($row['fcolor']) and $row['fcolor']!=0 and in_array($row['fcolor'], $fcolor_ids)==false) 
          $fcolor_ids[]=$row['fcolor'];
        if (isset($row['ppcolor']) and $row['ppcolor']!=0 and in_array($row['ppcolor'], $ppcolor_ids)==false) 
          $ppcolor_ids[]=$row['ppcolor'];
        if (isset($row['sub_type']) and $row['sub_type']!=0 and in_array($row['sub_type'], $sub_type_ids)==false) 
          $sub_type_ids[]=$row['sub_type'];
        if (isset($row['subalbum_size']) and $row['subalbum_size']!=0 and in_array($row['subalbum_size'], $subalbum_size_ids)==false) 
          $subalbum_size_ids[]=$row['subalbum_size'];
        if (isset($row['subpaper_type']) and $row['subpaper_type']!=0 and in_array($row['subpaper_type'], $subpaper_type_ids)==false) 
          $subpaper_type_ids[]=$row['subpaper_type'];
        if (isset($row['subcover_code']) and $row['subcover_code']!=0 and in_array($row['subcover_code'], $subcover_code_ids)==false) 
          $subcover_code_ids[]=$row['subcover_code'];
        if (isset($row['subcoll_design']) and $row['subcoll_design']!=0 and in_array($row['subcoll_design'], $subcoll_design_ids)==false) 
          $subcoll_design_ids[]=$row['subcoll_design'];
        break;  
      case 'gks_acc_inv':
        $row['eidi']=array();
        $row['products_count']=0; $data['ftypes']['main']['products_count']='INTEGER';
        $row['products_sets']=0; $data['ftypes']['main']['products_sets']='INTEGER';

        $id_acc_inv_ids[]=$row['id_acc_inv'];
        if (isset($row['delivery_id_8']) and $row['delivery_id_8']!=0 and in_array($row['delivery_id_8'], $delivery_id_8_ids)==false) 
          $delivery_id_8_ids[]=$row['delivery_id_8'];
        
        
        
      default:
        
        break;
    }
    
    
    $rows[]=$row;
  }

  $id_order_array=array();
  if (count($id_order_ids)>0) {
    $sql_other="SELECT gks_orders_products.*, 
    gks_eshop_products.product_code, gks_eshop_products.product_photo, gks_eshop_products.product_descr_small, gks_eshop_products.product_descr_big, 
    gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
    gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
    gks_eshop_pricelist.pricelist_descr,
    
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr, 
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr, 
    gks_aade_katigoria_telon.aade_katigoria_telon_descr, 
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
    product_fpa_pososto* 100 as product_fpa_pososto100
    
    FROM (((((((gks_orders_products 
    LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product) 
    LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_eshop_fpa ON gks_orders_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
    LEFT JOIN gks_eshop_pricelist ON gks_orders_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_orders_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
    LEFT JOIN gks_aade_katigoria_xartosimou ON gks_orders_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou) 
    LEFT JOIN gks_aade_katigoria_telon ON gks_orders_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
    LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_orders_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron
    
    WHERE gks_orders_products.order_id in (".implode(',',$id_order_ids).")
    ORDER BY gks_orders_products.id_order_product;";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    
    
    while ($row_other = $result_other->fetch_assoc()) {
      $id_order_array[$row_other['order_id']][]=$row_other;
    }
    
    foreach ($rows as $i => $row) {
      if (isset($id_order_array[$row['id_order']])) $rows[$i]['eidi']=$id_order_array[$row['id_order']];
      $rows[$i]['products_count']=count($rows[$i]['eidi']);
      
      $products_sets=array();
      foreach ($rows[$i]['eidi'] as $eidos) {
        $parts=explode(',',trim_gks($eidos['product_set']));
        foreach ($parts as $myset) {
          $myset=trim_gks($myset);
          if ($myset!='') {
            if (isset($products_sets[$myset])==false) $products_sets[$myset]=array();
            $products_sets[$myset][]= $eidos['id_order_product'];
          }
        }
      }
      $rows[$i]['products_sets']=count($products_sets);
    }
    
    unset($id_order_array);
    
    
    gks_export_excel_get_ftypes($data['ftypes']['eidi'],$result_other);
    //echo '<pre>';print_r($data);die();

    
  }
  
  $id_acc_inv_array=array();
  if (count($id_acc_inv_ids)>0) {

    
    $sql_other="SELECT gks_acc_inv_products.*, 
    gks_eshop_products.product_code, gks_eshop_products.product_photo, gks_eshop_products.product_descr_small, gks_eshop_products.product_descr_big, 
    gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
    gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
    gks_eshop_pricelist.pricelist_descr,
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_type, 
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_type,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_type,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr,
    
    gks_aade_katigoria_telon.aade_katigoria_telon_type, 
    gks_aade_katigoria_telon.aade_katigoria_telon_descr,
    gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_descr,
    product_fpa_pososto* 100 as product_fpa_pososto100
    FROM ((((((((gks_acc_inv_products 
    LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product) 
    LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
    LEFT JOIN gks_eshop_pricelist ON gks_acc_inv_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
    LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
    LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
    LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
    LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi
    
    WHERE gks_acc_inv_products.acc_inv_id in (".implode(',',$id_acc_inv_ids).")
    ORDER BY gks_acc_inv_products.id_acc_inv_product;";    
    
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    
    
    while ($row_other = $result_other->fetch_assoc()) {
      $id_acc_inv_array[$row_other['acc_inv_id']][]=$row_other;
    }
    
    foreach ($rows as $i => $row) {
      if (isset($id_acc_inv_array[$row['id_acc_inv']])) $rows[$i]['eidi']=$id_acc_inv_array[$row['id_acc_inv']];
      $rows[$i]['products_count']=count($rows[$i]['eidi']);
      
      $products_sets=array();
      foreach ($rows[$i]['eidi'] as $eidos) {
        $parts=explode(',',trim_gks($eidos['product_set']));
        foreach ($parts as $myset) {
          $myset=trim_gks($myset);
          if ($myset!='') {
            if (isset($products_sets[$myset])==false) $products_sets[$myset]=array();
            $products_sets[$myset][]= $eidos['id_acc_inv_product'];
          }
        }
      }
      $rows[$i]['products_sets']=count($products_sets);
    }
    
    unset($id_acc_inv_array);
    
    
    gks_export_excel_get_ftypes($data['ftypes']['eidi'],$result_other);
    //echo '<pre>';print_r($data);die();

    
  }  
  
  
  //print '<pre>';print_r($rows);die();
  //print '<pre>';print_r($id_order_array);die();
  
    
  $delivery_id_8_array=array();
  if (count($delivery_id_8_ids)>0) {
    $sql_other="SELECT id_warehouse,warehouse_name FROM gks_warehouses where id_warehouse in (".implode(',',$delivery_id_8_ids).")";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $delivery_id_8_array[$row_other['id_warehouse']]=$row_other['warehouse_name'];
    }
  }
  $order_type_array=array();
  if (count($order_type_ids)>0) {
    $sql_other="select * FROM cmsb_categories";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $order_type_array[$row_other['num']]=$row_other['name_el'];
    }
  }
  $book_type_array=array();
  if (count($book_type_ids)>0) {
    $sql_other="select * FROM cmsb_bundle_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $book_type_array[$row_other['num']]=$row_other['title_el'];
    }
  }
  $printing_type_array=array();
  if (count($printing_type_ids)>0) {
    $sql_other="select * FROM cmsb_printing_types";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $printing_type_array[$row_other['num']]=$row_other['subtitle'];
    }
  }
  $present_type_array=array();
  if (count($present_type_ids)>0) {
    $sql_other="select * FROM cmsb_bundle_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $present_type_array[$row_other['num']]=$row_other['title_el'];
    }
  }
  $box_type_array=array();
  if (count($box_type_ids)>0) {
    $sql_other="select * FROM cmsb_bundle_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $box_type_array[$row_other['num']]=$row_other['title_el'];
    }
  }
  $subtype_array=array();
  if (count($subtype_ids)>0) {
    $sql_other="select * FROM gks_cmsb_subtype";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $subtype_array[$row_other['id_subtype']]=$row_other['subtype_descr'];
    }
  }
  $cover_type_array=array();
  if (count($cover_type_ids)>0) {
    $sql_other="select * FROM gks_cmsb_cover_type";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $cover_type_array[$row_other['id_cover_type']]=$row_other['cover_type_descr'];
    }
  }
  $book_size_array=array();
  if (count($book_size_ids)>0) {
    $sql_other="select * FROM cmsb_book_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $book_size_array[$row_other['num']]=$row_other['size'];
    }
  }
  $album_size_array=array();
  $album_size_array[999]=gks_lang('Άλλη διάσταση');
  if (count($album_size_ids)>0) {
    $sql_other="SELECT * FROM cmsb_albums";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      
      
      $album_size_array[$row_other['num']]=$row_other['size_open'].($row_other['wooden']==1 ? ' (+wooden)' : '');
    }
  }
  $present_size_array=array();
  if (count($present_size_ids)>0) {
    $sql_other="SELECT * FROM cmsb_present_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $present_size_array[$row_other['num']]=$row_other['size'];
    }
  }
  $box_size_array=array();
  if (count($box_size_ids)>0) {
    $sql_other="SELECT * FROM cmsb_box_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $box_size_array[$row_other['num']]=$row_other['size'];
    }
  }
  $box_color_array=array();
  if (count($box_color_ids)>0) {
    $sql_other="SELECT * FROM cmsb_box_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $box_color_array[$row_other['num']]=$row_other['color'];
    }
  }
  $coating_array=array();
  if (count($coating_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_coating";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $coating_array[$row_other['id_coating']]=$row_other['coating_descr'];
    }
  }
  $print_paper_array=array();
  if (count($print_paper_ids)>0) {
    $sql_other="SELECT * FROM cmsb_papers";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $print_paper_array[$row_other['num']]=$row_other['name'].($row_other['canvas']==1 ? ' (+canvas)' : '');
    }
  }
  $paper_size_array=array();
  if (count($paper_size_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_paper_size";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $paper_size_array[$row_other['paper_size_descr']]=$row_other['paper_size_descr'];
    }
  }
  $present_color_array=array();
  if (count($present_color_ids)>0) {
    $sql_other="SELECT * FROM cmsb_present_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $present_color_array[$row_other['num']]=$row_other['color'];
    }
  }
  $pass_color_array=array();
  if (count($pass_color_ids)>0) {
    $sql_other="SELECT * FROM cmsb_present_attr";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $pass_color_array[$row_other['num']]=$row_other['color'];
    }
  }
  $printype_array=array();
  if (count($printype_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_printype";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $printype_array[$row_other['id_printype']]=$row_other['printype_descr'];
    }
  }
  $foiltype_array=array();
  if (count($foiltype_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_foiltype";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $foiltype_array[$row_other['id_foiltype']]=$row_other['foiltype_descr'];
    }
  }
  $cover_code_array=array();
  if (count($cover_code_ids)>0) {
    $sql_other="SELECT * FROM cmsb_book_covers";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $cover_code_array[$row_other['num']]=$row_other['code'];
    }
  }
  $coll_design_array=array();
  if (count($coll_design_ids)>0) {
    $sql_other="SELECT * FROM cmsb_collections";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $coll_design_array[$row_other['num']]=$row_other['code'];
    }
  }
  $pack_type_array=array();
  if (count($pack_type_ids)>0) {
    $sql_other="SELECT * FROM cmsb_bundle_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $pack_type_array[$row_other['num']]=$row_other['title_el'];
    }
  }
  $thickness_array=array();
  if (count($thickness_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_thickness";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $thickness_array[$row_other['id_thickness']]=$row_other['thickness_descr'];
    }
  }
  $lamination_array=array();
  if (count($lamination_ids)>0) {
    $sql_other="SELECT * FROM cmsb_after_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $lamination_array[$row_other['num']]=$row_other['title_el'];
    }
  }
  $mounting_array=array();
  if (count($mounting_ids)>0) {
    $sql_other="SELECT * FROM cmsb_after_records";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $mounting_array[$row_other['num']]=$row_other['title_el'].($row_other['frame'] == 1 ? ' (+frame)' : '');
    }
  }
  $telaro_array=array();
  if (count($telaro_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_telaro";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $telaro_array[$row_other['id_telaro']]=$row_other['telaro_descr'];
    }
  }
  $frame_array=array();
  if (count($frame_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_frame";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $frame_array[$row_other['id_frame']]=$row_other['frame_descr'];
    }
  }
  $fcolor_array=array();
  if (count($fcolor_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_fcolor";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $fcolor_array[$row_other['id_fcolor']]=$row_other['fcolor_descr'];
    }
  }
  $ppcolor_array=array();
  if (count($ppcolor_ids)>0) {
    $sql_other="SELECT * FROM gks_cmsb_ppcolor";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $ppcolor_array[$row_other['id_ppcolor']]=$row_other['ppcolor_descr'];
    }
  }
  $sub_type_array=array();
  if (count($sub_type_ids)>0) {
    $sql_other="SELECT * FROM cmsb_categories";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $sub_type_array[$row_other['num']]=$row_other['name_el'];
    }
  }
  $subalbum_size_array=array();
  $subalbum_size_array[999]=gks_lang('Άλλη διάσταση');
  if (count($subalbum_size_ids)>0) {
    $sql_other="SELECT * FROM cmsb_albums";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      
      $subalbum_size_array[$row_other['num']]=$row_other['size_open'].($row_other['wooden']==1 ? ' (+wooden)' : '');
      
      
    }
  }
  $subpaper_type_array=array();
  if (count($subpaper_type_ids)>0) {
    $sql_other="SELECT * FROM cmsb_papers";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $subpaper_type_array[$row_other['num']]=$row_other['name'];
    }
  }
  $subcover_code_array=array();
  if (count($subcover_code_ids)>0) {
    $sql_other="SELECT * FROM cmsb_book_covers";
    $result_other = $db_link->query($sql_other);        
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $subcover_code_array[$row_other['num']]=$row_other['code'];
    }
  }
  $subcoll_design_array=array();
  if (count($subcoll_design_ids)>0) {
    $sql_other="SELECT * FROM cmsb_collections";
    $result_other = $db_link->query($sql_other);
    if (!$result_other) {debug_mail(false,'error sql',$sql_other);die('sql error');}
    while ($row_other = $result_other->fetch_assoc()) {
      $subcoll_design_array[$row_other['num']]=$row_other['code'];
    }
  }
  
  
  foreach ($rows as &$row) {
    foreach ($data['main'] as $i => $field) {
      if (isset($field['a'])) {
        $fa_array=array();
        if (count($field['a'])==1) { //mono ena pedio p.x. [mylast_name]
          $a=$field['a'][0];
          $fa=substr($a,1,strlen($a)-2);
          $fa_array[]=$fa;
        } else if (count($field['a'])>=2) { // pano apo ena pedio p.x. [mylast_name] [mylast_name]
          $value=$field['f'];
          foreach ($field['a'] as $a) {
            $fa=substr($a,1,strlen($a)-2);
            $fa_array[]=$fa;
          }
        }
                    
        
        foreach ($fa_array as $fa) {
  
          switch ($fa) {
            //integer to empty
            case 'open_pages':if ($row[$fa] === "0") $row[$fa]='';break;            
            case 'subopen_pages':if ($row[$fa] === "0") $row[$fa]='';break;            
            case 'qty':if ($row[$fa] === "0") $row[$fa]='';break;            
            case 'subqty':if ($row[$fa] === "0") $row[$fa]='';break;            


            case 'subcoll_design':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($subcoll_design_array[$row[$fa]])) $row[$fa]=$subcoll_design_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'subcover_code':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($subcover_code_array[$row[$fa]])) $row[$fa]=$subcover_code_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'subpaper_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($subpaper_type_array[$row[$fa]])) $row[$fa]=$subpaper_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              //echo '<pre>';print_r($row);die();
              break;
            case 'subalbum_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($subalbum_size_array[$row[$fa]])) $row[$fa]=$subalbum_size_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'sub_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($sub_type_array[$row[$fa]])) $row[$fa]=$sub_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'ppcolor':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($ppcolor_array[$row[$fa]])) $row[$fa]=$ppcolor_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'fcolor':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($fcolor_array[$row[$fa]])) $row[$fa]=$fcolor_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'frame':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($frame_array[$row[$fa]])) $row[$fa]=$frame_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'telaro':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($telaro_array[$row[$fa]])) $row[$fa]=$telaro_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'mounting':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($mounting_array[$row[$fa]])) $row[$fa]=$mounting_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'lamination':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($lamination_array[$row[$fa]])) $row[$fa]=$lamination_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'thickness':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($thickness_array[$row[$fa]])) $row[$fa]=$thickness_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'pack_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($pack_type_array[$row[$fa]])) $row[$fa]=$pack_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'coll_design':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($coll_design_array[$row[$fa]])) $row[$fa]=$coll_design_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'cover_code':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($cover_code_array[$row[$fa]])) $row[$fa]=$cover_code_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'foiltype':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($foiltype_array[$row[$fa]])) $row[$fa]=$foiltype_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'printype':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($printype_array[$row[$fa]])) $row[$fa]=$printype_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'pass_color':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($pass_color_array[$row[$fa]])) $row[$fa]=$pass_color_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'present_color':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($present_color_array[$row[$fa]])) $row[$fa]=$present_color_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'paper_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($paper_size_array[$row[$fa]])) $row[$fa]=$paper_size_array[$row[$fa]]; //else $row[$fa]=$row[$fa]; //mia einai array, mia einai text
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'print_paper':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($print_paper_array[$row[$fa]])) $row[$fa]=$print_paper_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'coating':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($coating_array[$row[$fa]])) $row[$fa]=$coating_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'box_color':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($box_color_array[$row[$fa]])) $row[$fa]=$box_color_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'box_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($box_size_array[$row[$fa]])) $row[$fa]=$box_size_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'present_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($present_size_array[$row[$fa]])) $row[$fa]=$present_size_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'album_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($album_size_array[$row[$fa]])) $row[$fa]=$album_size_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'book_size':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($book_size_array[$row[$fa]])) $row[$fa]=$book_size_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'cover_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($cover_type_array[$row[$fa]])) $row[$fa]=$cover_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'subtype':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($subtype_array[$row[$fa]])) $row[$fa]=$subtype_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'box_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($box_type_array[$row[$fa]])) $row[$fa]=$box_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'present_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($present_type_array[$row[$fa]])) $row[$fa]=$present_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'printing_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($printing_type_array[$row[$fa]])) $row[$fa]=$printing_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'book_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($book_type_array[$row[$fa]])) $row[$fa]=$book_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'order_type':
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($order_type_array[$row[$fa]])) $row[$fa]=$order_type_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
            case 'delivery_id_8':
              //echo '<pre>'; print $fa."\n"; print $row[$fa]."\n"; print_r($delivery_id_8_array);die();
              $data['ftypes']['main'][$fa]='TEXT';
              if (isset($delivery_id_8_array[$row[$fa]])) $row[$fa]=$delivery_id_8_array[$row[$fa]]; //else $row[$fa]='';
              if ($row[$fa] === "0") $row[$fa]='';
              break;
  
  
  
            case 'parastatiko':
              $data['ftypes']['main'][$fa]='VARCHAR';
              if ($row['parastatiko']==0) $row[$fa]=gks_lang('Απόδειξη');
              if ($row['parastatiko']==1) $row[$fa]=gks_lang('Τιμολόγιο');
              break;
            case 'gks_sex':
              $data['ftypes']['main'][$fa]='VARCHAR';
              if ($row['gks_sex']==1) $row[$fa]=gks_lang('Άρρεν');
              if ($row['gks_sex']==2) $row[$fa]=gks_lang('Θύλη');
              break;
            case 'roles':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=getUserRoleDescr($row['ID']);
              break;
            case 'groups':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=getUserGroups($row['ID']);
              break;
            case 'order_state':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=getOrderStateDescr($row['order_state']);
              break;
            case 'inv_state':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=getAccInvStateDescr($row['inv_state']);
              break;
            case 'transfer_reservation_status':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=getTransferReservationStatusDescr($row['transfer_reservation_status']);
              break;
            case 'pliroteo':
              $data['ftypes']['main'][$fa]='DOUBLE';
              $row[$fa]=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];
              break;
            case 'company_sub_title':
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=(!empty($row[$fa]) ? $row[$fa] : gks_lang('Κεντρικό'));
              break;
             
             case 'payment_acquirer_name_via_calc':
              $temp=trim_gks($row['payment_acquirer_name']);
              if (trim_gks($row['tropos_pliromis_via'])!='') {
                $temp=trim_gks($row['tropos_pliromis_via']).' via '.$temp;
              }
              $data['ftypes']['main'][$fa]='TEXT';
              $row[$fa]=$temp;
              break;            
          }
          
        }
      }      
  
    }
  }
  unset($row);
  //print '<pre>';print_r($rows);die();
  
  
  $r=1;
  $rc_last=1;
  foreach ($rows as $row) {
  	$r++;
    foreach ($data['main'] as $i => $field) {
      $rc= $export_excel_start_row + $r;
      $ic= $export_excel_start_col + $i + 1;
      $rc_last=$rc;
      
      $rowspan=1;
      if (isset($data['eidi']) and count($data['eidi']) > 0) {
        if (isset($row['eidi']) and count($row['eidi'])>0) {
          $rowspan=count($row['eidi']);
        }
      }
      
      if (isset($field['a'])) {
        if (count($field['a'])==1) { //mono ena pedio p.x. [mylast_name]
          $a=$field['a'][0];
          $fa=substr($a,1,strlen($a)-2);

          
          if (isset($data['ftypes']['main'][$fa]) and isset($row[$fa])) {
            switch ($data['ftypes']['main'][$fa]) {
              case 'TEXT':
                $value = trim_gks($row[$fa]);
                $value = str_replace("\n\r","<br>",$value);
                $value = str_replace("\r\n",'<br>',$value);
                $value = str_replace('\r\n','<br>',$value);
                $value = str_replace('\r','<br>',$value);
                $value = str_replace('\n','<br>',$value);
                $value = str_replace('<br>',"\n",$value);
                //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);        
                $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);        
                //$mysheet->getCell([$ic,$rc])->getStyle()->getAlignment()->setWrapText(true);
                $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getAlignment()->setWrapText(true);
                break;
              case 'VARCHAR':
              case 'CHAR':
                //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($row[$fa]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($row[$fa]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                break;  
              
              case 'TINYINT':
              case 'SMALLINT':
              case 'INTEGER':
              case 'FLOAT':
              case 'DOUBLE':
              case 'BIGINT':
              case 'MEDIUMINT':
              case 'DECIMAL':
                //$mysheet->setCellValue([$ic,$rc],$row[$fa]);
                $mysheet->setCellValueByColumnAndRow($ic,$rc,$row[$fa]);
                break;
              
              case 'DATE':
              case 'DATETIME':
                $row_date=strtotime($row[$fa]);
                if (isset($field['_time']) and $field['_time']!=0) $row_date=_time_user($row_date,$field['_time']);
                
                //$mysheet->setCellValue([$ic,$rc],  PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row_date));
                $mysheet->setCellValueByColumnAndRow($ic,$rc,  PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row_date));
                if (isset($field['format']) and $field['format']!='') {
                  //$mysheet->getCell([$ic,$rc])->getStyle()->getNumberFormat()->setFormatCode($field['format']); // check \my\vendor\phpoffice\phpspreadsheet\src\PhpSpreadsheet\Style\NumberFormat.php
                  $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getNumberFormat()->setFormatCode($field['format']); // check \my\vendor\phpoffice\phpspreadsheet\src\PhpSpreadsheet\Style\NumberFormat.php
                } else {
                  //$mysheet->getCell([$ic,$rc])->getStyle()->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME
                  $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME
                }
                break;    
              
              default:
                $error_text='agnostos typos field (2) '.$data['ftypes']['main'][$fa].' '.$fa;
                if (in_array($error_text,$errors)==false) $errors[]=$error_text;
            }
          }
        } else if (count($field['a'])>=2) { // pano apo ena pedio p.x. [mylast_name] [mylast_name]
          $value=$field['f'];
          $parast_empty=$field['f'];
          foreach ($field['a'] as $a) {
            $fa=substr($a,1,strlen($a)-2);
            if (isset($data['ftypes']['main'][$fa])) {
              if (isset($row[$fa])) $temp=trim_gks($row[$fa]); else $temp='';
              $value=str_replace($a, $temp, $value);
              $parast_empty=str_replace($a, '', $parast_empty);
            }
          }
          $value=trim_gks($value);
          $parast_empty=trim_gks($parast_empty);
          
          if ($value!='' and $value!=$parast_empty) {
            //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
          }
        }
        
        if ($rowspan > 1) {
          
          //$myrange=PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ic).
          //         $rc.':'.
          //         PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ic).
          //         ($rc+($rowspan-1));
          //$mysheet->mergeCells($myrange);
          //$mysheet->getCell([$ic,$rc])->getStyle()->getAlignment()->setVertical('center');

          $mysheet->mergeCellsByColumnAndRow($ic,$rc,$ic,$rc+($rowspan-1));
          $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getAlignment()->setVertical('center');
          
        }
      }
    }
    
    
    // gks_orders_products
    //echo '<pre>'; print_r($row); die();
    if (isset($data['eidi']) and count($data['eidi']) > 0) {
      if (isset($row['eidi']) and count($row['eidi'])>0) {

        //echo '<pre>'; print_r($row); die();
        if (count($row['eidi'])>0) $r--; //gia na jekina to proto eidos stin idia grammi me tin paraggelia
        
        foreach ($row['eidi'] as $erow) {
        	$r++;
        	
          foreach ($data['eidi'] as $i => $field) {
            $rc= $export_excel_start_row + $r;
            $ic= $i + $eidi_col_start + 1;
            $rc_last=$rc;
            
            if (isset($field['a'])) {
              if (count($field['a'])==1) { //mono ena pedio p.x. [mylast_name]
                $a=$field['a'][0];
                $fa=substr($a,1,strlen($a)-2);
      
                
                if (isset($data['ftypes']['eidi'][$fa]) and isset($erow[$fa])) {
                  switch ($data['ftypes']['eidi'][$fa]) {
                    case 'TEXT':
                      $value = trim_gks($erow[$fa]);
                      $value = str_replace("\n\r","<br>",$value);
                      $value = str_replace("\r\n",'<br>',$value);
                      $value = str_replace('\r\n','<br>',$value);
                      $value = str_replace('\r','<br>',$value);
                      $value = str_replace('\n','<br>',$value);
                      $value = str_replace('<br>',"\n",$value);
                      //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);        
                      $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);        
                      //$mysheet->getCell([$ic,$rc])->getStyle()->getAlignment()->setWrapText(true);
                      $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getAlignment()->setWrapText(true);

                      break;
                    case 'VARCHAR':
                    case 'CHAR':
                      //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($erow[$fa]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                      $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($erow[$fa]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                      break;  
                    
                    case 'TINYINT':
                    case 'SMALLINT':
                    case 'INTEGER':
                    case 'FLOAT':
                    case 'DOUBLE':
                    case 'BIGINT':
                    case 'MEDIUMINT':
                    case 'DECIMAL':
                      //$mysheet->setCellValue([$ic,$rc],$erow[$fa]);
                      $mysheet->setCellValueByColumnAndRow($ic,$rc,$erow[$fa]);
                      break;
                    
                    case 'DATE':
                    case 'DATETIME':
                      $row_date=strtotime($erow[$fa]);
                      if (isset($field['_time']) and $field['_time']!=0) $row_date=_time_user($row_date,$field['_time']);
                      
                      //$mysheet->setCellValue([$ic,$rc],  PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row_date));
                      $mysheet->setCellValueByColumnAndRow($ic,$rc,  PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row_date));
                      if (isset($field['format']) and $field['format']!='') {
                        //$mysheet->getCell([$ic,$rc])->getStyle()->getNumberFormat()->setFormatCode($field['format']); // check \my\vendor\phpoffice\phpspreadsheet\src\PhpSpreadsheet\Style\NumberFormat.php
                        $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getNumberFormat()->setFormatCode($field['format']); // check \my\vendor\phpoffice\phpspreadsheet\src\PhpSpreadsheet\Style\NumberFormat.php
                      } else {
                        //$mysheet->getCell([$ic,$rc])->getStyle()->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME
                        $mysheet->getCellByColumnAndRow($ic,$rc)->getStyle()->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME
                      }
                      break;    
                    
                    default:
                      $error_text='agnostos typos field (2) '.$data['ftypes']['eidi'][$fa].' '.$fa;
                      if (in_array($error_text,$errors)==false) $errors[]=$error_text;
                  }
                }
              } else if (count($field['a'])>=2) { // pano apo ena pedio p.x. [mylast_name] [mylast_name]
                $value=$field['f'];
                foreach ($field['a'] as $a) {
                  $fa=substr($a,1,strlen($a)-2);
                  if (isset($data['ftypes']['eidi'][$fa])) {
                    if (isset($erow[$fa])) $temp=trim_gks($erow[$fa]); else $temp='';
                    $value=str_replace($a, $temp, $value);
                  }
                }
                $value=trim_gks($value);
                if ($value!='') {
                  //$mysheet->getCell([$ic,$rc])->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                  $mysheet->getCellByColumnAndRow($ic,$rc)->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
              }
            }
          }

          
          
        }        
        //end eidos
        
      }
    }
    
    
  }
  

  //echo $mysheet->getCellByColumnAndRow(5, 10)->getCoordinate();  die();
  //$mysheet->setCellValueByColumnAndRow(1, $rc_last+2, '=SUM(H2:H'.$rc_last.')');

  if (isset($data['footer'])) {
    foreach ($data['footer'] as $mycell) {
      if (isset($mycell['formula'])) {
        $formula=$mycell['formula'];
        $formula=str_replace('[last_row]',$rc_last,$formula);
        for($lrc=0;$lrc<=100;$lrc++) {
          $formula=str_replace('[last_row+'.$lrc.']',$rc_last+$lrc,$formula);
        }
        $mysheet->setCellValueByColumnAndRow($mycell['col'], $rc_last+$mycell['row'], $formula);
      } else if (isset($mycell['string'])) {
        $mysheet->getCellByColumnAndRow($mycell['col'], $rc_last+$mycell['row'])->setValueExplicit(trim_gks($mycell['string']), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      }
    }
  }
  
  
  foreach ($data['main'] as $i => $field) {
    if ($field['f']!='') {
      $ic= $export_excel_start_col + $i + 1;
      if (isset($field['max_width']) and $field['max_width']>0) {
        $mysheet->getColumnDimensionByColumn($ic)->setWidth($field['max_width']);
      } else {
        $mysheet->getColumnDimensionByColumn($ic)->setAutoSize(true);
      }
    }
  } 
  
  
  if (isset($data['eidi']) and count($data['eidi']) > 0) {
    foreach ($data['eidi'] as $i => $field) {
      if ($field['f']!='') {
        $ic= $eidi_col_start + $i + 1;
        if (isset($field['max_width']) and $field['max_width']>0) {
          $mysheet->getColumnDimensionByColumn($ic)->setWidth($field['max_width']);
        } else {
          $mysheet->getColumnDimensionByColumn($ic)->setAutoSize(true);
        }
      }
    }
  }  
  
  // exei provlima i PHP otan energopoithei to autofilter
  $mysheet->setAutoFilter($mysheet->calculateWorksheetDimension());
  //$mysheet->setAutoFilter('A1:E20');
  //$columnLetter = PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex(10);
  $columnLetter_start = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($export_excel_start_col + 1);
  $columnLetter_end = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($export_excel_start_col + count($data['main']) + count($data['eidi'])); 
  $cell_range= $columnLetter_start.($export_excel_start_row + 1).':'.$columnLetter_end.$rc_last;
  //echo $cell_range;die();
  $mysheet->setAutoFilter($cell_range);
  
  //echo $columnLetter; die();
  
  
  //$mysheet->freezePane('A1');
  //$mysheet->freezePane([$export_excel_start_col + 1, $export_excel_start_row + 2]);
  $mysheet->freezePaneByColumnAndRow($export_excel_start_col + 1, $export_excel_start_row + 2);  

  // Rename worksheet
  $mysheet->setTitle($export_excel_descr);
  
  
  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $objPHPExcel->setActiveSheetIndex(0);
  
  
  if (count($errors)>0) debug_mail(false,'export excel errors',implode("\n",$errors));
  
    //header('Content-Type: application/vnd.ms-excel'); //.xls
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // .xlsx
  //greeklish
  $export_file_name=$export_excel_filename_prefix.'_'.$export_excel_descr.'_'.showDate(time(), 'Y-m-d His', 1).'.xlsx';
  header('Content-Disposition: attachment;filename="'.$export_file_name.'"');
  header('Cache-Control: max-age=0');
  // If you're serving to IE 9, then the following may be needed
  header('Cache-Control: max-age=1');
  
  // If you're serving to IE over SSL, then the following may be needed
  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
  header ('Pragma: public'); // HTTP/1.0
  
  
  $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
  $objWriter->save('php://output');
  exit;


}


function gks_pivot_table_convert_to_excel($source,$mytable,$export_excel_descr) {
  global $GKS_SITE_HUMAN_NAME;
  
  
  $ret=array('success' => false, 'message' => base64_encode('general error on pivot table convert to excel'),'downloadfile'=> '');
  
  if ($source=='')  {$ret['message']=base64_encode(gks_lang('Δεν έχει ορισθεί η πηγή'));    return $ret;}
  if ($mytable=='') {$ret['message']=base64_encode(gks_lang('Δεν έχει ορισθεί ο πίνακας')); return $ret;}
  
  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="details_body"?>'.$mytable) === false) {
    $ret['message']=base64_encode(gks_lang('Δεν βρέθηκε κώδικας HTML')); return $ret;
  }  
  $tables = $dom->getElementsByTagName('table');  
  if ($tables->length < 1) {$ret['message']=base64_encode(gks_lang('Δεν βρέθηκε ο πίνακας στον κώδικα HTML')); return $ret;}
  
  $objPHPExcel = new PhpOffice\PhpSpreadsheet\Spreadsheet();
  $objPHPExcel->getProperties()->setCreator($GKS_SITE_HUMAN_NAME)
  							 ->setLastModifiedBy($GKS_SITE_HUMAN_NAME)
  							 ->setTitle($export_excel_descr)
  							 ->setSubject($export_excel_descr)
  							 ->setDescription($export_excel_descr)
  							 ->setKeywords($export_excel_descr.' '.$GKS_SITE_HUMAN_NAME)
  							 ->setCategory($export_excel_descr.' '.$GKS_SITE_HUMAN_NAME);
  
  $mystyle_thf= [
              'fill' => [
                'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '00e6EEEE'],
              ],
            ];
              
  $mystyle_thb= [
              'borders' => [
                'outline' => [
                  'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  'color' => array('argb' => '00000000'),
                ],
              ],
            ];

//    $styleArray = array(
//        'borders' => array(
//            'outline' => array(
//                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
//                'color' => array('argb' => 'FFFF0000'),
//            ),
//        ),
//    );
    
  $mysheet = $objPHPExcel->getActiveSheet()->setTitle($export_excel_descr);
  
  


  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');
  
  //echo '<pre>';
  //echo 'mytrs->length '.$mytrs->length."\n";
  $maxcolumn=0;
  
  $column_jump_to=array();
  for ($i = 0; $i < $mytrs->length; $i++) {
    //echo 'mytr ['.$i."]\n";
    //var_dump($mytrs[$i]);
    $mytds = $mytrs[$i]->childNodes;
    //echo 'mytds->length '.$mytds->length."\n";
    $jumps=0;
    $prev_ecolumn=0;
    for ($j = 0; $j < $mytds->length; $j++) {
      if ($maxcolumn < $j) $maxcolumn=$j;
      $nodeName=$mytds[$j]->nodeName;
      
      $class=trim_gks((string)$mytds[$j]->getAttribute('class'));
      
      $colspan=intval((string)$mytds[$j]->getAttribute('colspan'));
      if ($colspan<=0) $colspan=1;
      
      $rowspan=intval((string)$mytds[$j]->getAttribute('rowspan'));
      if ($rowspan<=0) $rowspan=1;
      //echo $rowspan;die();
      
      $erow=$i + 1;
      $ecolumn=$j + $jumps + 1;
      $ecolumn=$prev_ecolumn+1;
      
      //echo 'start erow:'.$erow.' ecolumn:'.$ecolumn.' jumps:'.$jumps.' | ';
      
      $steps=0;
      for ($gg=$ecolumn; $gg < $ecolumn+100; $gg++) {
        if (isset($column_jump_to[$gg][$erow])) {
          $steps++;
        } else {
          break;  
        }
      }
      $ecolumn+=$steps;
      
      $value='';
      $value_type='string';
      if ($nodeName=='th') {
        $value=$mytds[$j]->textContent;
          
        
        //$mysheet->getCell([$ecolumn,$erow])->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle_thf)->applyFromArray($mystyle_thb);
        $mysheet->getCellByColumnAndRow($ecolumn,$erow)->setValueExplicit(trim_gks($value), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)->getStyle()->applyFromArray($mystyle_thf)->applyFromArray($mystyle_thb);
        if ($class=='pvtColLabel') {
          //$mysheet->getCell([$ecolumn,$erow])->getStyle()->getAlignment()->setHorizontal('center');
          $mysheet->getCellByColumnAndRow($ecolumn,$erow)->getStyle()->getAlignment()->setHorizontal('center');
        } else if ($class=='pvtTotalLabel') {
          //$mysheet->getCell([$ecolumn,$erow])->getStyle()->getAlignment()->setHorizontal('right');
          $mysheet->getCellByColumnAndRow($ecolumn,$erow)->getStyle()->getAlignment()->setHorizontal('right');
        } 
        if ($rowspan>1) {
          //$mysheet->getCell([$ecolumn,$erow])->getStyle()->getAlignment()->setVertical('center');
          $mysheet->getCellByColumnAndRow($ecolumn,$erow)->getStyle()->getAlignment()->setVertical('center');          
        }
      } else if ($nodeName=='td') {
        //$value=$mytds[$j]->textContent;
        $temp=trim_gks((string)$mytds[$j]->getAttribute('data-value'));
        if ($temp!='') $value=floatval($temp); 
        if ($value<>0) {
          //$mysheet->setCellValue([$ecolumn,$erow],$value);
          $mysheet->setCellValueByColumnAndRow($ecolumn,$erow,$value);
        }
      }
      
      if ($colspan>1 or $rowspan>1) {
        //$myrange=PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ecolumn).
        //         $erow.':'.
        //         PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ecolumn+($colspan-1)).
        //         ($erow+($rowspan-1));
        //$mysheet->mergeCells([$ecolumn,$erow],[$ecolumn+($colspan-1),$erow+($rowspan-1)]);
        //$mysheet->getStyle([$ecolumn,$erow],[$ecolumn+($colspan-1),$erow+($rowspan-1)])->applyFromArray($mystyle_thb);
        $mysheet->mergeCellsByColumnAndRow($ecolumn,$erow,$ecolumn+($colspan-1),$erow+($rowspan-1));
        $mysheet->getStyleByColumnAndRow($ecolumn,$erow,$ecolumn+($colspan-1),$erow+($rowspan-1))->applyFromArray($mystyle_thb);
        
      }
      
      //echo '<pre>';var_dump($mytds[$j]);die();
      //echo 'i:'.$i.' j:'.$j.' erow:'.$erow.' ecolumn:'.$ecolumn.' jumps:'.$jumps.' steps:'.$steps.' nodeName:'.$nodeName.' colspan:'.$colspan.' rowspan:'.$rowspan.' class:'.$class.' value:'.$value."\n";
      
      //if ($colspan>1) $jumps+=($colspan-1);
      
      
      
      if ($rowspan>1) {
        for ($cc=0;$cc < $colspan;$cc++) {
          for ($rr=0;$rr < $rowspan;$rr++) {
            if (!($cc==0 and $rr==0)) {
              $column_jump_to[$ecolumn + $cc][$erow + $rr]=true;
            }
          }
        }
        //echo '<pre>';var_dump($column_jump_to);die();
      } else if ($colspan>1) {
        for ($cc=2;$cc <= $colspan;$cc++) {
          $column_jump_to[$ecolumn + $cc - 1][$erow]=true;
        }
      }
      $prev_ecolumn=$ecolumn;
      //if ($i==10 and $j==0) break 2; //echo '<pre>';print_r($column_jump_to);die();
      
      
    }
    
  }
  //echo '<pre>';print_r($column_jump_to);//die();
  
  for ($j = 0; $j < $maxcolumn; $j++) {
    $mysheet->getColumnDimensionByColumn($j+1)->setAutoSize(true);
  }
  
  $export_file_name=$export_excel_descr.'_'.showDate(time(), 'Y-m-d His', 1).'.xlsx';    
  $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
  if (file_exists(GKS_SITE_PATH.'tmp/')==false) {
    if (@mkdir(GKS_SITE_PATH.'tmp/' , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',GKS_SITE_PATH.'tmp/');
      $ret['message']=base64_encode('can not create dir: tmp'); return $ret;
    }
  }
  $objWriter->save(GKS_SITE_PATH.'tmp/'.$export_file_name);
  //$objWriter->save('php://output');
  
  $ret['downloadfile']='admin-get-file.php?fs=tmp&file='.rawurlencode($export_file_name);
  $ret['message']=base64_encode('OK'); 
  $ret['success']=true;

  
  return $ret;
  
}
