/**
 * Checkbox Menu Item
 * 
 * @param {object} settings
 */
ContextMenu.ui.checkbox = function( settings, ctxMenu ) 
{
    this.settings = settings;
    this.ctxMenu = ctxMenu;
    this.id = ContextMenu.items.generateID(); // Item ID, not HTML ID
    this.icon = $('<i>').addClass(settings.icon);
    this.text = $('<span>').addClass('contextmenu-item-text').html(settings.text);
    this.element = $('<li>').addClass('contextmenu-checkbox')
                            .attr('data-item-id', this.id)
                            .append(this.icon)
                            .append(this.text);

    if(settings.disabled) this.disable();
    if(settings.checked) this.check();
    if(typeof settings.id !== 'undefined') this.element.attr('id',settings.id);
    this.registerEventListeners();
};

ContextMenu.ui.checkbox.prototype.registerEventListeners = function()
{
    if(this.settings.disabled) return;
    
    var self = this,
        obj = $.extend({},self.settings,{instance: self});

    this.element.on('mouseenter',function(e){
        if( !self.isDisabled() )
            self.ctxMenu.selector.select(self.getID());
            
        if(typeof self.settings.hover === 'function')
            self.settings.hover.call(obj,e);
    });
    this.element.on('mouseleave',function(e){
        self.ctxMenu.selector.deselect();
    });
    
    // Click event
    this.element.click(function(e){
        self.toggle();
        if(typeof self.settings.click === 'function')
        {
            self.settings.click.call(obj,e);
        }
    });
};

ContextMenu.ui.checkbox.prototype.disable = function()
{
    this.element.addClass('contextmenu-disabled');
};

ContextMenu.ui.checkbox.prototype.isDisabled = function()
{
    return this.element.hasClass('contextmenu-disabled');
};


ContextMenu.ui.checkbox.prototype.check = function()
{
    this.element.addClass('contextmenu-checked');
};

ContextMenu.ui.checkbox.prototype.uncheck = function()
{
    this.element.removeClass('contextmenu-checked');
};

ContextMenu.ui.checkbox.prototype.toggle = function()
{
    this.element.toggleClass('contextmenu-checked');
};

ContextMenu.ui.checkbox.prototype.isChecked = function()
{
    return this.element.hasClass('contextmenu-checked');
};

ContextMenu.ui.checkbox.prototype.getID = function()
{
    return this.id;
};