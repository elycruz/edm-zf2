/**
 * File name and purpose here
 * http://elycruz.com/
 *
 * Copyright 2010, Ely De La Cruz
 * Released under the MIT and GPL Version 2 licenses.
 * http://elycruz.com/code-license
 * @todo Ajaxify all controllers for admin sections
 */

/**
 * Index filter select box class
 */
function IndexFilterSelectElement(key)
{
    var modName = arguments[1] || 'edm-admin',
    controllerName = arguments[2] || 'term-taxonomy',
    actionName = arguments[3] || 'index';
    
    return $('#' + key).change(function()
    {
        // Initialize edm
        edm.useModule = true;
        edm.init();
        
        // Prelims
        var value = $(this).get(0);
        value = value.options[value.selectedIndex].value;

        // Set parameter
        edm.request 
                . setModuleName(modName)
                . setControllerName(controllerName)
                . setActionName(actionName) 
                . setParam(key, value)
                //; trace(var_dump(edm.request.toString()));
                // Make request
                . make();
        
    }); // End change function
} 