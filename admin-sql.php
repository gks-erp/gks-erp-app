<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('MySQL Query Browser');
$nav_active_array=array('manage','manage_settings','manage_sql');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__sql','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



include_once('_my_header_admin.php');
?>
<style>
#results table th {
  min-width:100px;
}
#results table #gks_th_aa {
  min-width:unset;
}
.CodeMirror {
  height:150px !important;  
}

.preset_query {
  font-size: 0.8rem;
  height:1.1rem;
  cursor:pointer;
  overflow:hidden;
}
.preset_query:hover {
  background-color:#eeeeee;
}
.gks_preset_query_parent {
  height: 204px;  
}
.gks_preset_query {
  position: absolute;
  left:0px;
  top: 0px;
  bottom:0px;
  width:100%;
  background-color1: #f2f3f5; 
  padding: 10px; 
  overflow: auto;
  overflow-x: hidden;
  font-size: 14px;
}
.gks_preset_query::-webkit-scrollbar {width: 5px;}
.gks_preset_query::-webkit-scrollbar-track {background: #c0dac4; }
.gks_preset_query::-webkit-scrollbar-thumb {background: #458c50;}
.gks_preset_query::-webkit-scrollbar-thumb:hover {background: #23aa38;} 

</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('SQL');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sql');?> > 
          <div class="form-group row">
            <div class="col-md-12">
              <textarea id="mysql_query" class="form-control form-control-sm">select now() as mynow, version() as ver</textarea>
            </div>
          
          </div>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:center;">
              <button type="button" class="btn btn-primary" id="submit_button_sql_run"><?php echo gks_lang('Εκτέλεση');?></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('history');?> > 
          <div class="form-group row">
            <div class="col-md-12">
              <div class="gks_preset_query_parent">
                <div class="gks_preset_query" id="from_user_history" >
                  
<?php
                  $sql="select `sql` from gks_sql_log where user_id=".$my_wp_user_id." order by id_sql_log desc limit 1000";
                  $result = $db_link->query($sql);
                  if (!$result) {
                    debug_mail(false,'sql error',$sql);
                    echo 'sql error';
                  } else {
                    $exists=[];
                    while ($row = $result->fetch_assoc()) {
                      $query=trim_gks($row['sql']);
                      if (in_array($query,$exists)==false) {
                        echo '<div class="preset_query">'.$query.'</div>';
                        $exists[]=$query;
                      }                        
                    }
                  }
?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Παραδείγματα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('preset');?> > 
          <div class="form-group row">
            <div class="col-md-12">
              <div class="gks_preset_query_parent">
                <div class="gks_preset_query">
                  <div class="preset_query">SELECT now() as mynow</div>
                  <div class="preset_query">SELECT version() as ver</div>
                  <div class="preset_query">SELECT count(*) as cc from gks_stat_stat</div>
                  <div class="preset_query">SELECT count(*) as cc from gks_stat_queue</div>
                  <div class="preset_query">SELECT count(*) as cc from gks_stat_ips</div>
                  <div class="preset_query">SELECT current_user() AS user, DATABASE() AS db</div>
                  <div class="preset_query">SHOW DATABASES</div>
                  <div class="preset_query">SHOW TABLES</div>
                  <div class="preset_query">SHOW TABLES like 'gks_%'</div>
                  <div class="preset_query">SHOW FULL TABLES</div>
                  <div class="preset_query">SHOW TABLE STATUS</div>
                  <div class="preset_query">SHOW TRIGGERS</div>
                  <div class="preset_query">SHOW CREATE TABLE gks_sql_log</div>
                  <div class="preset_query">SELECT 
    table_name,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb,
    table_rows
FROM information_schema.tables
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC</div>
                  <div class="preset_query">SHOW PROCESSLIST</div>
                  <div class="preset_query">SHOW FULL PROCESSLIST</div>
                  <div class="preset_query">SHOW STATUS LIKE 'Uptime'</div>
                  <div class="preset_query">SHOW STATUS LIKE 'Threads_connected'</div>
                  <div class="preset_query">SHOW STATUS LIKE 'Queries'</div>
                  <div class="preset_query">SHOW GLOBAL STATUS LIKE 'Bytes_%'</div>
                  <div class="preset_query">SHOW VARIABLES LIKE 'max%'</div>
                  <div class="preset_query">SHOW VARIABLES LIKE '%timeout%'</div>
                  <div class="preset_query">SHOW VARIABLES LIKE '%charset%'</div>
                  <div class="preset_query">delete from gks_sql_log where user_id=<?php echo $my_wp_user_id;?></div>
                </div>  
              </div>  
            </div>  
          </div>
        </div>
      </div>
    </div>

    
  </div>
</div>

<h2 style="text-align:center"><?php echo gks_lang('Αποτέλεσμα');?></h2>
<div id="results" style="margin:15px;font-size: 0.8rem;">
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>



jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  var mysql_query_editor = CodeMirror.fromTextArea(document.getElementById("mysql_query"), {
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
      },
      "F11": function(cm) {
        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
      },
      "Esc": function(cm) {
        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
      }      
    },
    mode: {name: "sql",globalVars: true }, //
    lineNumbers: true,
    spellcheck: true,
    autocorrect: true,
    autocapitalize: true,
    indentUnit:2,
    tabSize: 2,
    indentWithTabs:false,
    smartIndent:true,
    autoCloseBrackets: true,
    styleActiveLine: true,
    lineWrapping: true,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    //viewportMargin: 20, //Infinity
  });
  
  
  $('#submit_button_sql_run').click(function() {
    var user_query=mysql_query_editor.getValue().trim();
    datasend='';
    datasend+='&query=' + encodeURIComponent($.base64.encode(user_query));
    //console.log(datasend);
    
    var found_prev_query=false;
    $('#from_user_history .preset_query').each(function() {
      temp=$(this).text().trim();
      if (temp==user_query) {
        found_prev_query=true;
        return;
      }
    });
    
    if (found_prev_query==false) {
      $('#from_user_history').prepend('<div class="preset_query preset_query_last_add">'+user_query+'</div>');
      $('.preset_query_last_add').click(preset_query_click).removeClass('preset_query_last_add');
    }
    
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-sql-exec.php',
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
            if (data.mytype=='SELECT') {
              $('#results').html(data.results);
              $('#results .mydivexpand').click(gks_mydivexpand_click);
            } else {
              $('#results').html('<div class="alert alert-success" role="alert">'+data.results+'</div>');
            }
            
					} else {
            $('#results').html('<div class="alert alert-danger" role="alert">'+$.base64.decode(data.message)+'</div>');
            
						//myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});      
    
  });
  
  function preset_query_click() {
    myquery=$(this).text();
    //console.log(myquery);
    mysql_query_editor.setValue(myquery);
  }
  $('.preset_query').click(preset_query_click);
  
  
  
});

</script>


<link rel="stylesheet" href="/my/js/codemirror-5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/hint/show-hint.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/fold/foldgutter.css" />
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/display/fullscreen.css">
<script src="/my/js/codemirror-5.65.16/lib/codemirror.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/sql/sql.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/show-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/sql-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/edit/closebrackets.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/selection/active-line.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldcode.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldgutter.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/brace-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/comment-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/display/fullscreen.js"></script>


  
<?php

//print '<pre>';
//print_r(get_included_files());
//print '</pre>';

include_once('_my_footer_admin.php');


