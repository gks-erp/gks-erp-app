/**
 * A wrapper for menu items.
 * 
 * @param {type} items
 * @returns {undefined}
 */
ContextMenu.menu = function( items, ctxMenu )
{
    this.ctxMenu = ctxMenu;
    this.build( items );
};

ContextMenu.menu.prototype.build = function( items )
{
    this.items = [];
    this.element = $('<ul>').addClass('contextmenu-menu');
    for( var i =0; i < items.length; i++ )
    {
        this.addItem( items[i] );
    }
};

ContextMenu.menu.prototype.addItem = function( item )
{
    var instance = new ContextMenu.ui[item.type](item,this.ctxMenu);
    this.items.push( $.extend({},item,{instance: instance}) );
    $(this.element).append( instance.element );
};

// Show and reposition if necessary
ContextMenu.menu.prototype.show = function()
{
    this.element.css({display:'block'});
    
    var w = this.element.outerWidth(),
        h = this.element.outerHeight(),
        t = this.element.offset().top - $(window).scrollTop(),
        l = this.element.offset().left;

    if( l+w > $(window).width() )
    {
        this.element.css({left:'-100%'});
    }
    if( t+h > $(window).height() )
    {
        this.element.css({top:$(window).height()-(t+h)});
    }
};

ContextMenu.menu.prototype.hide = function()
{
    // Hide and reset position
    this.element.css({display:'none',top:'0',left:'100%'});
};