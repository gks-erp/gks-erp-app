<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Εισαγωγή Barcodes από έγγραφο myData');
$nav_active_array=array('accounting','accounting_aade_docs');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_aade_docs','add',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$this_fs=''; if (isset($_GET['fs'])) $this_fs=trim_gks($_GET['fs']);
$this_file=''; if (isset($_GET['file'])) $this_file=trim_gks($_GET['file']);

$errors=[];
$htmls=[];
$products=array();
$epafi_id=0;
$afm_issuer='';

if ($this_fs=='') $errors[]=gks_lang('Δεν έχει ορισθεί ο φάκελος');
if ($this_file=='') $errors[]=gks_lang('Δεν έχει ορισθεί αρχείο');
if ($this_fs!='' and $this_file!='') {
  $htmls[]=gks_lang('Αρχείο').': <a href="admin-get-file.php?fs='.$this_fs.'&file='.$this_file.'&_cache='.time().rand(1000,9999).rand(1000,9999).'" target="_blank"><i class="fas fa-download" style="font-size:150%;"></i></a>';
}
if (count($errors)==0) {
  $fullpath=GKS_SITE_PATH.'tmp/'.$this_file;
  if (file_exists($fullpath)==false) $errors[]=gks_lang('Δεν βρέθηκε το αρχείο').': '.$fullpath;
}
$mark_doc='';
if (count($errors)==0) {
  try {
    
    $xml = new SimpleXMLElement(file_get_contents($fullpath), LIBXML_NOERROR);
    if (isset($xml->invoicesDoc)) {
      $NS = array( 
        'icls' => 'https://www.aade.gr/myDATA/incomeClassificaton/v1.0',
        'ecls' => 'https://www.aade.gr/myDATA/expensesClassificaton/v1.0',
      ); 
      $xml->registerXPathNamespace('icls', $NS['icls']);
      $xml->registerXPathNamespace('ecls', $NS['ecls']);
  
      //$namespaces = $xml->getNamespaces(true);
      //var_dump($namespaces);die();
      
      
      $invoicesDoc=$xml->invoicesDoc;
      $invoice=$xml->invoicesDoc->invoice;
  
      
      if (isset($invoice->mark)) {
        $mark_doc=(string)$invoice->mark;
        $htmls[]=gks_lang('ΜΑΡΚ').': '.$mark_doc;
      } else {
        $mark_doc='';
      }
  
      if (isset($invoice->issuer->vatNumber)) {
        $afm_issuer=(string)$invoice->issuer->vatNumber;
        $htmls[]=gks_lang('ΑΦΜ εκδότη').': '.$afm_issuer.' '.gks_get_user_from_afm($afm_issuer);
      } else {
        $afm_issuer='';
      }
      if (isset($invoice->counterpart->vatNumber)) {
        $afm_counterpart=(string)$invoice->counterpart->vatNumber;
        $htmls[]=gks_lang('ΑΦΜ αντισυμβαλλόμενου').': '.$afm_counterpart.' '.gks_get_user_from_afm($afm_counterpart);
      } else {
        $afm_counterpart='';
      }
      if (isset($invoice->invoiceDetails)) {
        foreach ($invoice->invoiceDetails as $product) {
          $lineNumber=0; if (isset($product->lineNumber)) $lineNumber=intval((string) $product->lineNumber);
          $itemCode='';  if (isset($product->itemCode))  $itemCode =trim((string)$product->itemCode);
          $itemDescr=''; if (isset($product->itemDescr)) $itemDescr=(string)$product->itemDescr;
          $TaricNo='';   if (isset($product->TaricNo))   $TaricNo=trim((string)$product->TaricNo);
          $quantity=0; if (isset($product->quantity)) $quantity=floatval((string) $product->quantity);
          $measurementUnit=0; if (isset($product->measurementUnit)) $measurementUnit=intval((string) $product->measurementUnit);
          

          $products[]=array(
            'lineNumber' => $lineNumber,
            'itemCode'=> $itemCode,
            'itemDescr'=> $itemDescr,
            'TaricNo'=> $TaricNo,
            'quantity' => $quantity,
            'measurementUnit' => $measurementUnit,
            'product_id' => 0,
            'product_descr' => '',
            'mustsave'=> false,
          );
        }
      }
    } else {
      $errors[]=gks_lang('Δεν βρέθηκε το invoicesDoc στην αρχή του αρχείου XML');
    }  
  } catch (Exception $e) { 
    $errors[]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
  }
}
if($afm_issuer!='') {
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_users.eponimia, gks_users.title, gks_users.afm
  FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null AND gks_users.afm In ('".$afm_issuer."')
  order by ".GKS_WP_TABLE_PREFIX."users.ID";
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
  $i=0;
  while ($row = $result->fetch_assoc()) {
    $i++;
    if ($epafi_id==0) $epafi_id=$row['ID'];
    $gks_nickname=trim_gks($row['gks_nickname']);
    if ($gks_nickname=='') $gks_nickname=trim_gks($row['title']);
    if ($gks_nickname=='') $gks_nickname=trim_gks($row['eponimia']);
    if ($gks_nickname=='') $gks_nickname='id: '.$row['ID'];
    $htmls[]=gks_lang('Επαφή').': '.($i==1?'<b>':'').'<a href="">'.$gks_nickname.'</a>'.($i==1?'</b>':'');
  }
}
if ($epafi_id==0) {
  $errors[]=gks_lang('Δεν βρέθηκε η σχετική επαφή. Δημιουργήστε πρώτα την επαφή για το ΑΦΜ').' '.$afm_issuer.
  ' <a href="admin-users-item.php?id=-1#createfromafm='.$afm_issuer.'|sup"><i class="fas fa-save"></i></a>';
}
if (count($products)==0) {
  $errors[]=gks_lang('Δεν βρέθηκαν είδη');
} else {
  $has_some_codes=false;
  foreach ($products as $key => $myp) {
    if ($myp['itemCode']!='' or $myp['TaricNo']!='') {
      $has_some_codes=true; break;
    }
  }
  if ($has_some_codes==false) {
    $errors[]=gks_lang('Τα είδη δεν έχουν κωδικούς');
  }
}
$sql_tempate1="SELECT gks_barcodes.product_id,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr<>'' THEN
        gks_eshop_products.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr
        END
    END
  ELSE gks_eshop_products.product_descr
END as product_descr_p
FROM (gks_barcodes 
LEFT JOIN gks_eshop_products ON gks_barcodes.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
";

$sql_tempate2="select * from (
SELECT gks_eshop_products.id_product,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr<>'' THEN
        gks_eshop_products.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr
        END
    END
  ELSE gks_eshop_products.product_descr
END as product_descr_p
FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
) as mytable
";

//echo '<pre>'.$sql_tempate1;die();

foreach ($products as &$myp) {
  $itemCode=$myp['itemCode'];
  if ($itemCode<>'') {
    if ($myp['product_id']==0) {
      $sql=$sql_tempate1." where gks_barcodes.barcode='".$db_link->escape_string($itemCode)."' and gks_barcodes.user_id=".$epafi_id;
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $myp['product_id']=intval($row['product_id']);
        $myp['product_descr']=trim_gks($row['product_descr_p']);
      }
    }
    if ($myp['product_id']==0) {
      $sql=$sql_tempate1." where gks_barcodes.barcode='".$db_link->escape_string($itemCode)."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $myp['product_id']=intval($row['product_id']);
        $myp['product_descr']=trim_gks($row['product_descr_p']);
        //$myp['mustsave']=true;
      }
    }
  }

  $itemDescr=$myp['itemDescr'];
  if ($itemDescr<>'') {
    if ($myp['product_id']==0) {
      $sql=$sql_tempate2." where product_descr_p like '".$db_link->escape_string($itemDescr)."'";
      //echo '<pre>'.$sql;die();
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');} 
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $myp['product_id']=intval($row['id_product']);
        $myp['product_descr']=trim_gks($row['product_descr_p']);
        $myp['mustsave']=true;
      }
    }
  }
} 
unset($myp);

$has_mustsave=false;
foreach ($products as $myp) if ($myp['mustsave']) {$has_mustsave=true;break;}


include_once('_my_header_admin.php');
?>
<style>
.fa-search-plus {
  color: goldenrod;
  cursor: pointer;
  vertical-align: middle; 
  font-size: 16px;
  margin-left: 8px; 
}
  
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
<?php if (count($htmls)>0) {?>      
    <div class="col-sm-12" style="margin-bottom:30px;">
<?php if (count($errors)>0) {?>
      <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading"><i class="fas fa-info-circle"></i> <?php echo gks_lang('Ο έλεγχος του αρχείου δεν έγινε επιτυχώς!');?></h4>
<?php } else {?>        
      <div class="alert alert-success" role="alert">
        <h4 class="alert-heading"><i class="fas fa-check-circle"></i> <?php echo gks_lang('Ο έλεγχος του αρχείου έγινε επιτυχώς!');?></h4>
<?php } ?>
        <p><?php echo implode('<br>',$htmls);?></p>
      </div>
    </div>
<?php }?>
<?php if (count($errors)>0) {?>
    <div class="col-sm-12">
      <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> <?php echo gks_lang('Σφάλμα!');?></h4>
        <p><?php echo implode('<br>',$errors);?></p>
        <hr>
        <p class="mb-0"><?php echo gks_lang('Ξαναδοκιμάστε την διαδικασία από την αρχή');?></p>
      </div>
    </div>
<?php } ?>
  </div>
</div>

     
<?php 
//echo '<pre>'; print_r($products);echo '</pre>'; 

$sql="select aade_eidos_posotitas_code,aade_eidos_posotitas_descr 
FROM gks_aade_eidos_posotitas 
where aade_eidos_posotitas_code>0
ORDER BY sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$mm=[];
while ($row = $result->fetch_assoc()) {
  $mm[$row['aade_eidos_posotitas_code']]=$row['aade_eidos_posotitas_descr'];
}

?>


<div style="margin:15px;<?php echo (count($errors)>0 ?'opacity:0.5;':'');?>">
<div style="margin:15px;text-align:center;">
  <?php echo gks_lang('Κάντε τις αντιστοιχίσεις και αποθηκεύστε τις αλλαγές');?>
</div>  
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" style="width:100%;" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" >#</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Γραμμή');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Κωδικός');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Taric No');?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo gks_lang('Περιγραφή');?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo gks_lang('Είδος στο ERP');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><span class="tooltispter" title="<?php echo gks_lang('Μονάδα μέτρησης');?>"></span><?php echo gks_lang('M.M.');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Ποσότητα');?></th>
  </tr>
</thead>
<tbody>
  
  
    <?php
    $i = 0;
    foreach ($products as $key => $myp) {
	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm"><?php echo ($i);?></th>
    <td class="mytdcm" nowrap><?php echo $myp['lineNumber'];?></td>
    <td class="mytdcm" nowrap><?php echo $myp['itemCode'];?></td>
    <td class="mytdcm" nowrap><?php echo $myp['TaricNo'];?></td>
    <td class="mytdcml"><?php echo $myp['itemDescr'];?></td>
    <td class="mytdcml">
      <?php if ($myp['itemCode']!='' or $myp['TaricNo']!='') {?>
      <input data-aa="<?php echo $key;?>" data-id="<?php echo $myp['product_id'];?>" class="gks_product_id form-control form-control-sm" 
      value="<?php echo $myp['product_descr'];?>" 
      style="width:calc(98% - 22px);display:inline;" 
      type="text" 
      placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
      <?php echo (count($errors)>0 ?'disabled':'');?>>  
      <a data-aa="<?php echo $key;?>" class="autocomplete_gks_product_id" tabindex="-1" href="admin-products-item.php?id=0" style="display:none"><i class="fas fa-search-plus" style="" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
      
      <?php } ?>
    </td>
    <td class="mytdcm" nowrap><?php 
      if (isset($mm[$myp['measurementUnit']])) echo $mm[$myp['measurementUnit']];
      else echo $myp['measurementUnit'];?></td>
    <td class="mytdcm" nowrap><?php echo $myp['quantity'];?></td>
  </tr>
<?php    
    }
?>
</tbody>
</table>
</div>

<div style="text-align:center;margin:50px;">
<button type="button" class="btn btn-primary" id="submit_button_ok_custom" <?php
  if ($has_mustsave==false) echo 'disabled';
  ?>><?php echo gks_lang('Αποθήκευση');?></button>
</div> 

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;


var from_php_products=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($products));?>'));

   
<?php echo from_php_global_vars_echo();?>
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible") && elem.prop('disabled')==false) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });  


  $('.gks_product_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:1,
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    minLength: 3,
    delay: 300, //default
    select: function( event, ui ) {
      $(event.target).attr('data-id',ui.item.id);
      aa=$(event.target).attr('data-aa');
      aa=parseInt(aa);if (isNaN(aa)) aa=-1;if (aa<0) return;
      $('.autocomplete_gks_product_id[data-aa=' + aa + ']').attr('href','admin-products-item.php?id='+ui.item.id).show();
      $('#submit_button_ok_custom').prop('disabled',false);
      from_php_products[aa].product_id=ui.item.id;
      from_php_products[aa].product_descr=ui.item.value;      
      need_save=true; 
    },
    change: function (event, ui) {
      if(!ui.item){
        $(event.currentTarget).val('').attr('data-id','0');
        aa=$(event.target).attr('data-aa');
        aa=parseInt(aa);if (isNaN(aa)) aa=-1;if (aa<0) return;
        $('.autocomplete_gks_product_id[data-aa=' + aa + ']').attr('href','#').hide();
        $('#submit_button_ok_custom').prop('disabled',false);
        from_php_products[aa].product_id=0;
        from_php_products[aa].product_descr='';
        need_save=true; 
      }
    }
  });
  
  $('#submit_button_ok_custom').click(function() {
    datasend='epafi_id=<?php echo $epafi_id;?>';
    datasend+='&mark=<?php echo $mark_doc;?>';
    datasend+='&products_str=' + encodeURIComponent($.base64.encode(JSON.stringify(from_php_products)));
     
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-acc-aade-docs-codes-exec.php',
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
					  myalert('ok:' + $.base64.decode(data.message),'', true);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
  });



  //generic
  gks_page_loading=false;


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
//db_close();
include_once('_my_footer_admin.php');
