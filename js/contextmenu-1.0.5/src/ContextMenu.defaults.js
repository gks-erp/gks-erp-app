/**
 * Default plugin options
 */
ContextMenu.defaults = {
    maxHeight:  null,
    minHeight:  null,
    maxWidth:   null,
    minWidth:   160,
    height:     null,
    width:      null,
    class:      null,   // Optional CSS class
    event:      'contextmenu', // Or [click|dblclick|hover|focus]
    selector:   null, // Repeat the selector here for dynamic binding
    hooks:      {show:null,hide:null,position:null},
    position:   {my:'left-25 top+5', at: 'mouse', children: false}, // Or {my:'left top', at: 'center bottom'} (using jQuery UI position
    autoHide:   false, // Or delay time in miliseconds
    transition: { speed: 0, type: 'none' },
    appendTo:   document.body,
    arrow:      'auto', // Or bottom, left, right, top (false for no arrow)
    items:      [],     // Or a function that takes the current event as an argument and returns a list of items
    click:      null,   // General purpose click function (overriden by item's click function)
    hover:      null    // General purpose hover function (overriden by item's hover function)
};