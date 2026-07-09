/**
 * A utility class for selecting and navigating between menu items.
 */
ContextMenu.selector = function( parent )
{
    this.parent = parent;
};

/**
 * Select a menu item
 * 
 * This function traverses the entire menu (including children) until the item 
 * with the matching id is found. If it is found, this.activeItem is set to the
 * matched item, and this.activeParents is set to the array of parent items
 * leading to the item.
 * 
 * @param {number} itemID The item id
 * @param {ContextMenu.menu} menu The containing menu (leave blank to use the root menu)
 * @param {array} parents An array containing the item's parent items, from furthest to closest
 * @returns {Boolean}
 */
ContextMenu.selector.prototype.select = function( itemID, menu, parents )
{
    if(typeof menu === 'undefined') menu = this.parent.items.root;

    for( var i = 0; i < menu.items.length; i++ )
    {
        var item = menu.items[i].instance;

        if(item.getID && item.getID() === itemID)
        {
            this.deselect(); // Prevent double selection
            
            if( $.isArray(parents) && parents.length > 0 )
            {
                $(parents).each(function(){
                    this.element.addClass('active');
                    this.submenu.show();
                });
                this.activeParents = parents;
            }
            else
                this.activeParents = undefined;
            
            item.element.addClass('active');
            this.activeItem = item;
            
            return true;
        }
        if(item.hasSubmenu && item.hasSubmenu())
        {
            // Add current item to active parents
            if( typeof parents === 'undefined' )
            {
                parents = [item];
            }
            else
            {
                parents.push(item);
            }
            
            // return true if the item is a in the submenu
            if(this.select(itemID, item.submenu, parents))
            {
                return true;
            }
            // Otherwise, remove the last added parent and continue
            parents.pop();
        }
    }
    return false;
};

/**
 * Deselect current item
 */
ContextMenu.selector.prototype.deselect = function()
{
    if(this.activeItem)
    {
        this.activeItem.element.removeClass('active');
        
        // Hide the submenu of the current element
        if( this.activeItem.hasSubmenu && this.activeItem.hasSubmenu() )
            this.activeItem.submenu.hide();
        
        this.activeItem = undefined;
        $(this.activeParents).each(function(){
            this.element.removeClass('active');
            this.submenu.hide();
        });
        this.activeParents = [];
    }
};

/**
 * Get the currently selected item
 * @returns {item}
 */
ContextMenu.selector.prototype.getSelection = function()
{
    return this.activeItem;
};

ContextMenu.selector.prototype.getFirstItem = function( menu )
{
    var items = menu ? menu.items : this.parent.items.root.items;
    for( var i = 0; i < items.length; i++ )
    {
        var item = items[i].instance;
        if( item.isDisabled && !item.isDisabled() )
        {
            return item;
        }
    }
    throw "This context menu has no selectable items";
};

/**
 * Get the next item in the current menu, ignoring children 
 * items and disabled items
 * @returns {Number} the item's id
 */
ContextMenu.selector.prototype.getNextItem = function()
{
    var menu = this.parent.items.root;
    if( !this.activeItem ) return this.getFirstItem();
    if(this.activeParents) menu = this.activeParents[this.activeParents.length-1].submenu;
    
    for( var i = 0; i < menu.items.length; i++ )
    {
        var item = menu.items[i].instance;
        if(item.id > this.activeItem.id && item.isDisabled && !item.isDisabled())
        {
            return item;
        }
    }
    return this.activeItem;
};

/**
 * Get the previous item in the current menu, ignoring children 
 * items and disabled items
 * @returns {Number} the item's id
 */
ContextMenu.selector.prototype.getPrevItem = function()
{
    var menu = this.parent.items.root;
    if( !this.activeItem ) return this.getFirstItem();
    if(this.activeParents) menu = this.activeParents[this.activeParents.length-1].submenu;
    
    for( var i = menu.items.length-1; i >= 0; i-- )
    {
        var item = menu.items[i].instance;
        if(item.id < this.activeItem.id && item.isDisabled && !item.isDisabled())
        {
            return item;
        }
    }
    return this.activeItem;
};

/**
 * Get the id of the first child of the currently active item
 * @returns {Number} the item's id
 */
ContextMenu.selector.prototype.getFirstChildItem = function()
{
    if( !this.activeItem ) return this.getFirstItem();
    if(this.activeItem.hasSubmenu && this.activeItem.hasSubmenu())
    {
        return this.getFirstItem(this.activeItem.submenu);
    }
    return this.activeItem;
};

/**
 * Get the id of the parent item of the currently active item
 * @returns {Number} the item's id
 */
ContextMenu.selector.prototype.getImmediateParentItem = function()
{
    if( !this.activeItem ) return this.getFirstItem();
    if( !this.activeParents ) return this.activeItem;
    return this.activeParents[this.activeParents.length-1];
};