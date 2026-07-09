/**
 * Menu positioning control class 
 * 
 * @param {ContextMenu} parent
 */
ContextMenu.position = function( parent ) 
{
    this.parent = parent;
};

ContextMenu.position.prototype.set = function(e) 
{
    this.element = this.getElement(e);
    this.offset  = {x: (e.pageX - $(this.element).offset().left), y: (e.pageY - $(this.element).offset().top)};
    this.target  = e.target;
    this.mouse   = {x:e.pageX, y:e.pageY};

    this.$position();
    this.parent.selector.deselect();
    this.parent.trigger('position',e);
};

ContextMenu.position.prototype.onResize = function() 
{
    if( this.parent.container.isVisible() )
    {
        this.$position();
    }
};

ContextMenu.position.prototype.$position = function() 
{
    if(typeof this.element === 'undefined') throw "Must use ContextMenu.position.set() prior to $position";
    
    var ctx      = this.parent,
        position = ctx.settings.position,
        mouse    = position.at === 'mouse',
        my       = position.my,
        at       = mouse ? 'left+'+this.offset.x+' '+'top+'+this.offset.y : position.at;

    $(this.parent.container.element).position({
        my: my,
        at: at,
        of: position.children !== false && $(this.target).closest(position.children).length ? $(this.target).closest(position.children) : this.element,
        collision: 'fit',
        using: function( pos, rel ) {
            rel.element.element.css({top:pos.top,left:pos.left});
            ctx.position.placeArrow( rel.element, rel.target );
        }
    });
};

ContextMenu.position.prototype.placeArrow = function( element, target ) 
{
    var ctx      = this.parent,
        settings = ctx.settings,
        arrow    = ctx.arrow,
        mouse    = ctx.settings.position.at === 'mouse';
            
    if( false !==  settings.arrow )
    {
        if('auto' === settings.arrow)
        {
            arrow.setPosition( mouse ? arrow.calcCoordsRelativePosition( this.mouse ) : arrow.calcTargetRelativePosition( element, target ) );
        }
        else
        {
            arrow.setPosition(settings.arrow);
        }
    }
}

/**
 * Returns the correct target element, even if the target element has been created dynamically
 * 
 * @param {event} e 
 * @returns {element}
 */
ContextMenu.position.prototype.getElement = function(e) 
{
    // If the selector argument is set, use it to check if the event's target element match
    if( null !== this.parent.settings.selector )
    {
        return $(e.target).closest(this.parent.settings.selector)
    }
    return this.parent.element;
}