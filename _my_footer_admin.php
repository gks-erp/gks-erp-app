<!-- footer -->

<?php 


if (isset($gks_header_footer_layout)==false or $gks_header_footer_layout=='full') { ?>
    <p class="gks_footer_last_p">&nbsp;</p>   
    <div style="clear: both;"></div>
    <div id="div_html_notification"><div id="div_notification_footer" style="display:none;"><input class="submit_button" type="submit" value="<?php echo gks_lang('Ορισμός όλων ως αναγνωσμένων');?>" id="cmdSetAllAsReadfloat" style="font-size:13px;"><img src="img/eye-icon-x.png?v=<?php echo $gks_cache_version.'.'.$gks_menu_version;?>" border="0" id="notif_img_hide" style="width:20px;vertical-align:top;cursor:pointer;padding-top: 4px;" title="<?php echo gks_lang('Απόκρυψη πλαισίου ειδοποιήσεων');?>"></div></div>  
    <div style="clear: both;"></div>


</gks_main_content> 


</gks_main_container> 


<div style="clear: both;"></div>  
    <nav id="gks_nav_session_footer" class="navbar navbar-dark bg-primary navbar-expand fixed-bottom1">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsbottom" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-md-center" id="navbarsbottom">
        <ul class="navbar-nav" style="align-items: center;">
          <li class="nav-item">
            <a class="navbar-brand" href="/"><img src="/my/_current/_img_site/logo2.png?v=<?php echo $gks_cache_version.'.'.$gks_menu_version;?>" height="20" alt=""></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="http://www.gks.gr" target="_blank">Powered by gks.gr</a>
          </li>
          <?php
    			//if (1==1 and (GKS_DEBUG or $my_wp_user_id==1)) {
    			if (true) {
    			  echo 
    			  '<li class="nav-item"><span class="nav-link">'.
    			  '<i class="fas fa-info-circle tooltipster" title="'.
    			  'PHP exec secs: '.number_format(microtime(true) - $dev_page_starttime,2,',','.'). '<br>'.
    			  'Memory: '.  number_format( memory_get_usage()/1024/1024,1,',','.').'MB<br>'.
    			  'Peak Memory: '.  number_format( memory_get_peak_usage()/1024/1024,1,',','.').'MB<br>'.
    			  '"></i></span></li>';
    			}
    			?>
			
        </ul>
      </div>
    </nav>
<?php } ?>

    <?php gks_erp_app_purchase_ads_footer_ad();?>
    <div class="waitmodal"></div>
    <!--
    <audio id="audioping" src="/my/audio/ping.mp3" type="audio/mpeg"></audio>
    <iframe src="/my/audio/silence.mp3" allow="autoplay" type="audio/mpeg" id="audio" style="display:none"></iframe>
    -->
    <audio id="audioping" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/my/audio/notif1.mp3" type="audio/mpeg"></audio>
    <script src="js/_gks_perm_ret_edit.js?v=<?php echo $gks_cache_version;?>"></script>
	  <?php gks_erp_app_purchase_ads_footer_script();?>	
  </body>
</html>
<?php
if (1==2 and $my_wp_user_id==1) {
print '<pre>';
//print_r($GLOBALS);
print_r(get_included_files());
}