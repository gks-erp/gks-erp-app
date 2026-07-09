/**
 * Context Menu Class
 * 
 * @param {element} element The element to bind the menu to
 * @param {type} options
 * @returns {ContextMenu}
 */
var ContextMenu = function( element, options ) 
{
    this.element      = element;
    this.settings     = options;
    this.items        = new ContextMenu.items(this);
    this.selector     = new ContextMenu.selector(this);
    this.events       = new ContextMenu.events(this);
    this.position     = new ContextMenu.position(this);
    this.arrow        = new ContextMenu.arrow(this);
    this.container    = new ContextMenu.container(this);
    
    this.build();
};

/**
 * Build the menu
 * 
 * Called when the widget is created, and later when changing options
 */
ContextMenu.prototype.build = function() 
{
    this.container.build();
    this.events.bind();
};

/**
 * 
 * @returns {undefined}
 */
ContextMenu.prototype.destroy = function() 
{
    this.container.destroy();
    this.events.unbind();
};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
ContextMenu.prototype.show = function(e) 
{   
    this.container.show(e);
};

/**
 * 
 * @returns {undefined}
 */
ContextMenu.prototype.hide = function(e) 
{
    this.container.hide(e);
};

/**
 * 
 * @param {type} hook
 * @param {type} e
 * @returns {undefined}
 */
ContextMenu.prototype.trigger = function( hook, e ) 
{
    var func = this.settings.hooks[hook];
    if(typeof func === 'function')
    {
        func.call(null,e);
    }
};