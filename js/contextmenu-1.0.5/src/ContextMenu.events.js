/**
 * Event binding utility class
 * 
 * @param {ContextMenu} parent
 */
ContextMenu.events = function( parent ) 
{
    this.parent = parent;
};

ContextMenu.events.prototype.bind = function() 
{
    this[this.parent.settings.event]();
    this.setKeyboardNavigation();
};

ContextMenu.events.prototype.unbind = function() 
{
    // TODO: Also remove events bound to the html element itself
    if( this.parent.settings.selector !== null )
    {
        $('html').off('click contextmenu mouseenter mouseleave dblclick focusin', this.parent.settings.selector);
    }
};

ContextMenu.events.prototype.hover = function() 
{
    var ctx = this.parent,
        selector = ctx.settings.selector;
    
    $(selector ? 'html' : ctx.element).on({
        mouseenter: function (e) {
            if( ctx.events.targetIsElement(e.target) )
            {
                ctx.show(e);
            }
        },
        mouseleave: function (e) {
            // Hide if not hovering on the menu itself
            if( !$(e.toElement).closest(ctx.container.element).length )
            {
                ctx.container.hide(e,ctx.settings.autoHide);
            }
        },
        mousemove: function (e) {
            // Reposition menu on mouse move (for cases when position.children is set)
            if( ctx.events.targetIsElement(e.target) && ctx.settings.position.children)
            {
                ctx.show(e);
            }
        }
    }, selector);
    
    // Hide the menu when the mouse leaves it, but only if it doesn't
    // go back to the triggering element
    this.parent.container.element.hover(
        function(e){
            ctx.container.cancelHide();
        },
        function(e){
            if(!$(e.toElement).closest(ctx.element).length)
            {
                ctx.container.hide(e,ctx.settings.autoHide);
            }
        }
    );
    
    $('html').on('click contextmenu', function(e){ctx.events.offEvent(e);});
};

ContextMenu.events.prototype.contextmenu = function() 
{
    var ctx = this.parent;
    
    $('html').on('contextmenu', ctx.settings.selector, function(e){ctx.events.onEvent(e);});
    $('html').on('click', function(e){ctx.hide(e);}); // A click anywhere hides the menu
};

ContextMenu.events.prototype.click = function() 
{
    var ctx  = this.parent;
    
    $('html').on('click', ctx.settings.selector, function(e){ctx.events.onEvent(e);});
    $('html').on('click contextmenu', function(e){ctx.events.offEvent(e);});
};

ContextMenu.events.prototype.focus = function() 
{
    var ctx  = this.parent;
    
    $('html').on('focusin', ctx.settings.selector, function(e){ctx.events.onEvent(e);});
    $('html').on('click contextmenu', function(e){ctx.events.offEvent(e);});
};

ContextMenu.events.prototype.dblclick = function() 
{
    var ctx  = this.parent;
    
    $('html').on('dblclick', ctx.settings.selector, function(e){ctx.events.onEvent(e);});
    $('html').on('click contextmenu', function(e){ctx.events.offEvent(e);});
};

ContextMenu.events.prototype.onEvent = function(e)
{
    var ctx  = this.parent;
    if( ctx.events.targetIsElement(e.target) )
    {
        e.preventDefault();
        ctx.show(e);
    }
    else
    {
        ctx.hide(e);
    }
}

ContextMenu.events.prototype.offEvent = function(e)
{
    if( !this.parent.events.targetIsElement(e.target) )
    {
        this.parent.hide(e);
    }
}

ContextMenu.events.prototype.targetIsElement = function( target ) 
{
    return $(target).closest( this.parent.settings.selector || this.parent.element ).length;
};

ContextMenu.events.prototype.setKeyboardNavigation = function()
{
    var p = this.parent;
            
    // Also catch keyup to prevent it's default behaviour
    $(document).on('keydown keyup',function(e){
        if(p.container.isVisible())
        {
            var selector = p.selector,
                c = e.which,
                prevItem = selector.getSelection(),
                nextItem;
            
            // Enter key pressed
            if(c === 13 && prevItem)
            {
                e.preventDefault();
                prevItem.element.trigger('click');
                return;
            }
            
            // Arrow keys
            if(c === 37 || c === 38 || c === 39 || c === 40)
            {
                e.preventDefault();
                
                // Only select on keydown
                if(e.type==='keyup') return;
                
                switch(c)
                {
                    case 37: // Left key
                        nextItem = selector.getImmediateParentItem();
                        break;
                    case 38: // Up key
                        nextItem = selector.getPrevItem();
                        break;
                    case 39: // Right key
                        nextItem = selector.getFirstChildItem();
                        break;
                    case 40: // Down key
                        nextItem = selector.getNextItem();
                        break;
                }
                
                // If the selections has changed, select the new item and trigger its hover event
                if(!prevItem || prevItem.getID() !== nextItem.id)
                {
                    if(prevItem) prevItem.element.trigger('mouseleave'); // This also triggers selector.deselect()
                    nextItem.element.trigger('mouseenter'); // This also triggers selector.select()
                }
                
                // Scroll the continer to show the item if 'maxHeight' was set
                if( null !== p.settings.maxHeight )
                {
                    var $item = nextItem.element,
                        $root = p.items.root.element,
                        itemHeight = $item.outerHeight(),
                        rootHeight = $root.outerHeight();
                    
                    // Item is below the bottom of the container
                    if( $item.offset().top + itemHeight > $root.offset().top + rootHeight )
                        $root.scrollTop( $root.scrollTop() + itemHeight );
                    
                    // Item is above the top of the container
                    if( $item.offset().top < $root.offset().top )
                        $root.scrollTop( $root.scrollTop() - itemHeight );
                }
            }
        }
    });
};