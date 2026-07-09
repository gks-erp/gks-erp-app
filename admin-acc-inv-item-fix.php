<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr

<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
 
$my_page_title='_invfix';
$nav_active_array=array();


db_open();
stat_record();



include_once('_my_header_admin.php');

$id_eftpos_transaction=0;
$sessionId='';

$is_ok=false;
if ($id<=0) {
  echo '<p>id is not set</p>' ;
} else {
  $sql="SELECT id_acc_inv, aade_send_date FROM gks_acc_inv WHERE id_acc_inv=".$id;
  $result = $db_link->query($sql);        
  if (!$result) die('sql error');
  
  
  if ($result->num_rows==0) {
    echo '<p>id not found</p>' ;
  } else {
    $row = $result->fetch_assoc();
    if (!empty($row['aade_send_date'])) {
      echo '<p>'.gks_lang('Έχει αποσταλεί στις').' '.$row['aade_send_date'].'</p>' ;
    } else {
      $sql="SELECT gks_acc_inv_payment.transaction_id, gks_eftpos_transaction.sessionId
      FROM gks_acc_inv_payment 
      LEFT JOIN gks_eftpos_transaction ON gks_acc_inv_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
      WHERE gks_acc_inv_payment.transaction_pa_with_id in (1,5,6)
      AND gks_acc_inv_payment.acc_inv_id=".$id." 
      AND gks_acc_inv_payment.transaction_id>0 
      AND gks_eftpos_transaction.id_eftpos_transaction Is Not Null 
      AND gks_eftpos_transaction.sessionId<>''
      ORDER BY gks_acc_inv_payment.id_acc_inv_payment DESC";
      $result = $db_link->query($sql);        
      if (!$result) die('sql error');
      if ($result->num_rows==0) {
        echo '<p>eftpos_transaction not found</p>' ;
      } else {
        $row = $result->fetch_assoc();
        $id_eftpos_transaction=intval($row['transaction_id']);
        $sessionId=trim_gks($row['sessionId']);
        echo '<pre>';
        echo 'id: '.$id."\r\n";
        echo 'id_eftpos_transaction: '.$id_eftpos_transaction."\r\n";
        echo 'sessionId: '.$sessionId."\r\n";
        echo '</pre>';
        $is_ok=true;
      }
      
    }
    
  }
}

if ($is_ok) {?>
<p>
<button id="rumme">timer</button>
</p>
<p id="resb1"></p>
<p>
<button id="myabort">abort</button>
</p>
<p id="resb2"></p>
<?php } ?>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
 

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#rumme').click(function() {
    datasend='';
    datasend+='&page=' + encodeURIComponent($.base64.encode('/my/admin-pos-run.php'));
    datasend+='&transaction_type=' + encodeURIComponent($.base64.encode('sale'));
    datasend+='&doc_id=' + '<?php echo $id;?>';
    datasend+='&sessionId=' + encodeURIComponent($.base64.encode('<?php echo $sessionId;?>'));
    datasend+='&id_eftpos_transaction=' + '<?php echo $id_eftpos_transaction;?>';
    

    
    $.ajax({
      url: 'admin-eftpos-transaction-dialog-timer.php',
      type: 'POST',
			cache: false,
			dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);

  		},
  		success: function( data ) {
  		  //console.log(data);
  		  html='<pre>' + JSON.stringify(data, null, 2) + '</pre>';
  		  
  		  $('#resb1').html(html);
  		}
    });  		  
  });
  
  $('#myabort').click(function() {

    //console.log('delete_run',eftpos_sessionId);

    datasend='';
    datasend+='&page=' + encodeURIComponent($.base64.encode('/my/admin-pos-run.php'));
    datasend+='&transaction_type=' + encodeURIComponent($.base64.encode('sale'));
    datasend+='&doc_id=' + '<?php echo $id;?>';
    datasend+='&sessionId=' + encodeURIComponent($.base64.encode('<?php echo $sessionId;?>'));
    datasend+='&id_eftpos_transaction=' + '<?php echo $id_eftpos_transaction;?>';
    $('body').addClass('myloading');
    $.ajax({
      url: 'admin-eftpos-transaction-dialog-abort.php',
      type: 'POST',
			cache: false,
			dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);
  			$('body').removeClass('myloading');
  		},
      success: function( data ) {
        $('body').removeClass('myloading');
        if (data.success == true) {
          //console.log(data);
          myalert('ok:'+gks_lang('Η αίτηση ακύρωσης έχει σταλεί επιτυχώς'));
        } else {
          myalert('error:' + $.base64.decode(data.message));
        }
      }
    });    
    
  });  
  
});
</script>


<?php

include_once('_my_footer_admin.php');


