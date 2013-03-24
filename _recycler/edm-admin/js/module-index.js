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
    new IndexFilterSelectElement('tuplesPerPage');
    new IndexFilterSelectElement('positionFilter');
    new IndexFilterSelectElement('statusFilter');
    new IndexFilterSelectElement('accessGroupFilter');

    // Make term taxonomy controls submit on change
    $('.term-taxonomy-control').each(function(){
        new AttributeSelectElement($(this),
            'set-term-taxonomy', 'termTaxonomyId');
    });

    // Make status controls submit on change
    $('.status-control').each(function(){
        new AttributeSelectElement($(this),
            'set-status', 'status');
    });

    // Make access group controls submit on change
    $('.user-group-control').each(function(){
        new AttributeSelectElement($(this),
            'set-user-group', 'accessGroup');
    });

    // List order elements
    $('.list-order-control').each(function() {
        new ListOrderTextInputElement($(this).attr('id'));
    });


    $('table a[title="delete"]').each(function(){
       var o = $(this);
       o.click(function(e){
            e.preventDefault();
            $('<p class="tsml">Are you sure you want to delete this element</p>').dialog({
			resizable: false,
			height: 240,
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