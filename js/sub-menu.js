/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

$('.dropdown-submenu > a').on("click", function(e) {
    
    var submenu = $(this);
    if (submenu.next('.dropdown-menu').hasClass('show')==false) {
      $('.dropdown-submenu .dropdown-menu').removeClass('show');
      submenu.next('.dropdown-menu').addClass('show');
    } else {
      $('.dropdown-submenu .dropdown-menu').removeClass('show');
    }
    e.stopPropagation();
});
$('.dropdown').on("hidden.bs.dropdown", function() {
    // hide any open menus when parent closes
    $('.dropdown-menu.show').removeClass('show');
});