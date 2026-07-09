/**
 * @see https://learn.jquery.com/jquery-ui/widget-factory/extending-widgets/
 * @see https://jqueryui.com/widget/
 */
$.widget( "custom.contextMenu", {
    // default options
    options: ContextMenu.defaults,
    // the constructor
    _create: function() 
    {
        // Change the element if the 'selector' option was provided
        // The original element is ignored
        if( this.options.selector !== null ) this.element = $(this.options.selector);
        this.contextMenu = new ContextMenu( this.element, this.options );
    },
    // called when created, and later when changing options
    _refresh: function() {this.contextMenu.build();},
    // events bound via _on are removed automatically
    // revert other modifications here
    _destroy: function() {this.contextMenu.destroy();},
    
    /*-------------------------------------*\
     * Plugin Methods
    \*-------------------------------------*/
    
    show: function(e){
        this.contextMenu.show(e);
        return this.element;
    },
    
    hide: function(e){
        this.contextMenu.hide(e);
        return this.element;
    },
    
    refresh: function(e){
        this.contextMenu.hide(e);
        this.contextMenu.show(e);
        return this.element;
    },
    
    isVisible: function(){
        return this.contextMenu.container.isVisible();
    },
    
    /**
     * Return the currently opened menu items. Only works if the menu is visible.
     */
    getItems: function(){
        var ctx = this.contextMenu;
        if( this.isVisible() )
        {
            return ctx.items.getAll();
        }
    },
    
    select: function(itemID){
        var ctx = this.contextMenu;
        if( ctx.container.isVisible() )
        {
            return ctx.selector.select(itemID);
        }
    }
});