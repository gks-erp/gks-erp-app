/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

jQuery(document).ready(function($) {
  //console.log('load _wp_gks_core.js');
  $('input[name=gks_source_url]').val(window.location.href);
  
  if (window.location.hash) {
    myhash=window.location.hash;
    pa1=myhash.split('#');
    if (pa1.length==2 && pa1[0]=='' && pa1[1].length>=44) {
      pa2=pa1[1].split('=');
      if (pa2.length==2 && pa2[0]=='gkssourlgid' && pa2[1].length==32) {
        $('input[name=gkssourlgid]').val(pa2[1]);
      }
    }
  }
  
  //gkssourlgid
  
});
