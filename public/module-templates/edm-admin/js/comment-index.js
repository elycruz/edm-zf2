/*!
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
    edm.init();
    
    // Make status controls submit on change
    $('.status-control').each(function(){
        new AttributeSelectElement($(this),
            'set-status', 'status');
    });
    
    // For every delete a href link
    $('table a[title="delete"]').each(function(){
       var o = $(this);
       o.click(function(e){
            e.preventDefault();
            $('<p class="tsml">Are you sure you want to delete' +
                'this element?</p>').dialog({
                        title: 'Post Delete Confirmation',
			resizable: false,
			height: 144,
			modal: true,
			buttons: {
				"Delete item": function() {
					$( this ).dialog( "close" );
                                        window.location = o.attr('href');
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
       });
    });
    
});