/**
 * Implements the menu's arrow element
 * 
 * @param {ContextMenu} parent
 */
ContextMenu.arrow = function( parent ) 
{
    this.parent = parent;
};

ContextMenu.arrow.prototype.setPosition = function( position ) 
{
    if(!this.parent.settings.arrow.match()) return;
    
    var positions = ['top','left','right','bottom'],
        prefix    = 'contextmenu-arrow-',
        classes   = prefix+positions.join(' '+prefix),
        container = this.parent.container.element;
        
    $(container)
        .removeClass(classes)
        .addClass(prefix+position);
};

/**
 * Calculate where the menu is relative to the mouse cursor and return
 * the position where the arrow should be placed
 */
ContextMenu.arrow.prototype.calcCoordsRelativePosition = function( coords ) 
{
    var mx = coords.x,
        my = coords.y,
        el = $(this.parent.container.element),
        ml = el.offset().left,
        mr = ml+el.width(),
        mt = el.offset().top,
        mb = mt+el.height();

    if( mx <= mr && mx >= ml )
    {
        if( my <= mt ) return 'top';
        if( my >= mb ) return 'bottom';
    }
    
    if( my <= mb && my >= mt )
    {
        if( mx <= ml ) return 'left';
        if( mx >= mr ) return 'right';
    }
}

/**
 * Calculate where the menu is relative to the target element and return
 * the position where the arrow should be placed
 */
ContextMenu.arrow.prototype.calcTargetRelativePosition = function( element, target )
{
    var el = element.left,
        er = element.left+element.width,
        et = element.top,
        eb = element.top+element.height,
        tl = target.left,
        tr = target.left+target.width,
        tt = target.top,
        tb = target.top+target.height;
        
    if( el > tl && el < tr && et > tt && et < tb ) return 'top'; // Menu is inside the target element
    if( eb <= tt ) return 'bottom'; // Menu is above the target element
    if( et >= tb ) return 'top'; // Menu is below the target element
    if( el >= tr ) return 'left'; // Menu is to the right of the target element
    if( er <= tl ) return 'right'; // Menu is to the left of the target element
};