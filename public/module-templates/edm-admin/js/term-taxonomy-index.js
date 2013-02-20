/**
 * File name and purpose here
 * http://elycruz.com/
 *
 * Copyright 2010, Ely De La Cruz
 * Released under the MIT and GPL Version 2 licenses.
 * http://elycruz.com/code-license
 *
 * Date: Sat Feb 13 22:33:48 2010 -0500
 */

$(function()
{
    // Filter elements
    new IndexFilterSelectElement('tuplesPerPage');
    new IndexFilterSelectElement('taxonomyFilter');
    new IndexFilterSelectElement('accessGroupFilter');
    new IndexFilterSelectElement('termGroupFilter');
    
    // List order elements
    $('.list-order-control').each(function() {
        new ListOrderTextInputElement($(this).attr('id'));
    });
    
});