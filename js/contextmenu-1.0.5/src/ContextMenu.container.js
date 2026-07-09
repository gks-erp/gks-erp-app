/**
 * A wrapper around all menu elements
 * 
 * @param {ContextMenu} parent
 */
ContextMenu.container = function( parent )
{
    this.parent = parent;
    this.createElement();
    
    // Don't set autoHide for hover event, since that event implements its own auto hiding functionality
    if( false !== this.parent.settings.autoHide && 'hover' !== this.parent.settings.event )
    {
        this.setAutoHide();
    }
    
    // Reposition menu when resizing
    $(window).on('resize', function(){
        parent.position.onResize();
    });
};

ContextMenu.container.prototype.createElement = function()
{
    var p = this.parent,
        t = p.settings.transition;
    this.element = $('<div>')
            .attr( 'id', "contextmenu" )
            .css({'animation-duration':t.speed+'ms'})
            .addClass('contextmenu-transition-'+t.type)
            .addClass(p.settings.class);
};

ContextMenu.container.prototype.build = function( e )
{
    var items = this.parent.items;
    items.build(e);
    
    // Don't build menu if there are no items
    if( items.isBuilt() && items.getAll().length )
    {
        $(this.element).append(this.parent.items.root.element);
        this.setMenuDimensions();
        this.isBuilt = true;
    }
    else
    {
        this.isBuilt = false;
    }
};

ContextMenu.container.prototype.setMenuDimensions = function()
{
    var s = this.parent.settings,
        m = this.parent.items.root.element;

    if(s.minHeight) m.css({'min-height':s.minHeight});
    if(s.minWidth) m.css({'min-width':s.minWidth});
    if(s.maxHeight) m.css({'max-height':s.maxHeight,'overflow-y':'auto','overflow-x':'hidden'});
    if(s.maxWidth) m.css({'max-width':s.maxWidth});
    if(s.height) m.css({'height':s.height,'overflow-y':'auto','overflow-x':'hidden'});
    if(s.width) m.css({'width':s.width});
};

ContextMenu.container.prototype.show = function(e)
{
    if(this.parent.items.isDynamic()) this.build(e);
    if(!this.isBuilt) return;
    
    this.cancelHide();
    
    if( this.isVisible() )
    {
        this.parent.position.set(e);
        return;
    }
    
    $(this.parent.settings.appendTo).append(this.element);
    this.parent.position.set(e);
    this.element.addClass('contextmenu-visible');
    this.parent.trigger('show',e);
};

ContextMenu.container.prototype.hide = function( e, delay )
{
    if( !this.isVisible() ) return;

    var ctx = this.parent;
    this.timeout = setTimeout(function(){
        
        ctx.container.element.removeClass('contextmenu-visible');
    
        // Hide menu after transition has finished
        ctx.container.detachTimeout = setTimeout(function(){
            ctx.selector.deselect();
            ctx.container.element.detach(); // Detach and keep events
        },ctx.settings.transition.speed);

        ctx.trigger('hide',e);
    },delay||0);
};

ContextMenu.container.prototype.cancelHide = function()
{
    clearTimeout(this.detachTimeout);
    clearTimeout(this.timeout);
};

/**
 * 
 * @returns {ContextMenu.prototype@pro;menu@pro;element@call;hasClass|Boolean}
 */
ContextMenu.container.prototype.isVisible = function() 
{
    return this.parent.items.isBuilt() && this.element.hasClass('contextmenu-visible');
};

ContextMenu.container.prototype.setAutoHide = function() 
{
    // this.timeout is also used outside of this function
    var ctx = this.parent;
    this.element.hover(
        function(e){
            ctx.container.cancelHide();
        },
        function(e){
            ctx.container.hide(e,ctx.settings.autoHide);
        }
    );
};

ContextMenu.container.prototype.destroy = function()
{
    this.element.remove();
};
