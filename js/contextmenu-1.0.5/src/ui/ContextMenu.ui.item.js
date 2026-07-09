/**
 * General Menu Item
 * 
 * @param {object} settings
 */
ContextMenu.ui.item = function( settings, ctxMenu ) 
{
    this.settings = settings;
    this.ctxMenu = ctxMenu;
    this.id = ContextMenu.items.generateID(); // Item ID, not HTML ID
    this.icon = $('<i>').addClass(settings.icon);
    this.text = $('<span>').addClass('contextmenu-item-text').html(settings.text);
    this.element = $('<li>').addClass('contextmenu-item')
                            .attr('data-item-id', this.id)
                            .append(this.icon)
                            .append(this.text);
    
    if(settings.disabled) this.disable();
    if(typeof settings.id !== 'undefined') this.element.attr('id',settings.id);
    this.createSubmenu();
    this.bindEvents();
};

ContextMenu.ui.item.prototype.disable = function()
{
    this.element.addClass('contextmenu-disabled');
};

ContextMenu.ui.item.prototype.isDisabled = function()
{
    return this.element.hasClass('contextmenu-disabled');
};

ContextMenu.ui.item.prototype.bindEvents = function()
{
    if(this.settings.disabled) return;
    
    var self = this,
        obj = $.extend({},self.settings,{instance: self});
    
    this.element.on('mouseenter',function(e){
        e.preventDefault();
        e.stopPropagation();
        
        if( !self.isDisabled() )
            self.ctxMenu.selector.select(self.getID());
        
        if( self.hasSubmenu() )
            self.submenu.show();
        
        if( typeof self.settings.hover === 'function' )
            self.settings.hover.call(obj,e);
    });
    
    this.element.on('mouseleave',function(e){
        e.preventDefault();
        e.stopPropagation();
        
        self.ctxMenu.selector.deselect();
        
        if( self.hasSubmenu() )
            self.submenu.hide();
    });
    
    this.element.on('click',function(e){
        if( typeof self.settings.click === 'function' )
            self.settings.click.call(obj,e);
        
        if( self.hasSubmenu() )
            self.submenu.hide();
    });
};

ContextMenu.ui.item.prototype.createSubmenu = function()
{
    if(this.settings.disabled) return;
    if( this.hasSubmenu() )
    {
        this.submenu = new ContextMenu.menu( this.settings.items, this.ctxMenu );
        this.element.addClass('contextmenu-submenu').append( this.submenu.element );
    }
};

ContextMenu.ui.item.prototype.hasSubmenu = function()
{
    return Array.isArray( this.settings.items ) && this.settings.items.length > 0;
};

ContextMenu.ui.item.prototype.getID = function()
{
    return this.id;
};