/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {
  if (typeof(from_php_perm_ret_edit) !== 'undefined') {
    if (from_php_perm_ret_edit==false) {
      //console.log('2',from_php_perm_ret_edit);  
      mypostform_elem=$('#mypostform');
      if (mypostform_elem.length>0) {
        mypostform_elem.find('.tagit').each(function() {
          $(this).find('.ui-autocomplete-input').prop('disabled', true); //.val('');
          $(this).find('.tagit-close').remove();
        });
      }  
    }
  }  
});



