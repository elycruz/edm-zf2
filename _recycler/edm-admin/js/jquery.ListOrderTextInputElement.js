/**
 * File name and purpose here
 * http://elycruz.com/
 *
 * Copyright 2010, Ely De La Cruz
 * Released under the MIT and GPL Version 2 licenses.
 * http://elycruz.com/code-license
 * @todo Incorporate collapsable table columns and an index options form for tuple index pages; I.e., post/index, term-taxonomy/index etc..
 * @todo Ajaxify all controllers for admin sections
 */

/**
 * Index List Order Text Input Element
 */
function ListOrderTextInputElement(id)
{
    var modName = arguments[1] || (edm.module || 'edm-admin'),
    controllerName = arguments[2] || (edm.controller || 'term-taxonomy'),
    actionName = arguments[3] || (edm.action || 'set-list-order');
    
    return $('#' + id).keyup(function(e)
    {
        // If the user hasn't typed enter do nothing
        if (e.keyCode != 13) {
            return;
        }

        // Prelims
        var field = $(this), itemId;
        itemId = field.attr('id').split('_')[1];

        try {
            // Initialize edm
            edm.init();
            
            //alert(print_r(edm.request)); return;

            // Set params for request and make request
            edm.request.clearParams()
                . setModuleName(modName)
                . setControllerName(controllerName)
                . setActionName(actionName)
                . setRouteDefinedParam(itemId)
                . setParam('listOrder', field.attr('value'));
                
            $.get(edm.request.toString(), function(data) {
                trace(var_dump(data));
            });
        }
        catch (e) {
            alert(e);
        }
        
    }); // End change function
} 