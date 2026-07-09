ContextMenu.items = function( parent )
{
    this.parent = parent;
};

ContextMenu.items.prototype.build = function( e )
{
    var settings = this.parent.settings;
    
    // Dynamically build the items
    if( this.isDynamic() )
    {
        // For the items to build dynamically, e must be set
        if( typeof e === 'undefined' ) return;
        
        // Remove the previously built menu
        if( this.isBuilt() ) 
        {
            this.root.element.remove();
            this.root = undefined;
        }
    }
    
    // Don't build menu if it has already been built
    if(this.isBuilt()) return;
        
    // Build nodes dynamically if a function was provided
    if(this.isDynamic())
    {
        this.nodes = settings.items.call(null,e);
    }
    // Or save a copy of the items array to leave the original set intact
    else
    {
        this.nodes = this.cloneItems( settings.items );
    }
    
    this.postProcess( this.nodes );
    ContextMenu.items.resetIDs();
    this.root = new ContextMenu.menu( this.getAll(), this.parent );
};

// Post process items after they have been created
ContextMenu.items.prototype.postProcess = function( items )
{
    for( var i = 0; i < items.length; i++ )
    {
        var item = items[i];
        
        // Set general purpose click/hover event if the item does not have one set
        if('item' === item.type || 'checkbox' === item.type)
        {
            if(!item.click) item.click = this.parent.settings.click;
            if(!item.hover) item.hover = this.parent.settings.hover;
        }
        if(item.hasSubmenu && item.hasSubmenu())
        {
            this.postProcess(item.submenu);
        }
    }
    
};

ContextMenu.items.prototype.cloneItems = function( items )
{
    var newItems = [];
    for( var i = 0; i < items.length; i++ )
    {
        // Copy each object
        newItems.push($.extend({},items[i]));
    }
    return newItems;
};

ContextMenu.items.prototype.getAll = function()
{
    if(typeof this.nodes !== 'undefined')
    {
        return this.nodes;
    }
    return [];
};

ContextMenu.items.prototype.isDynamic = function()
{
    return typeof this.parent.settings.items === 'function';
};

ContextMenu.items.prototype.isBuilt = function()
{
    return typeof this.root !== 'undefined';
};

// Used by selectable menu items like 'item' or 'checkbox' to generate a unique id
ContextMenu.items.generateID = function()
{
    if( typeof this.currentID === 'undefined' )
    {
        this.currentID = 0;
    }
    return this.currentID++;
};

ContextMenu.items.resetIDs = function()
{
    this.currentID = 0;
};