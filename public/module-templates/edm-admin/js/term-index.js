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
    var contentArea = $('#content-area');
    
    edm.init();
    new IndexFilterSelectElement('tuplesPerPage');
    //new IndexFilterSelectElement('termGroupFilter');
    
    $.get('/edm-admin-rest/term/get', function(data) {
        trace(var_dump(data));
        var t = $('<div class="module module-1 grid_12">' +
            '<div class="header"><span>Jquery Grid Test</span></div>' +
            '<div class="body"><div class="content">' +
            '<table id="test-table"><thead></thead><tbody></tbody><tfooter></tfooter></table>' +
            '</div></div><div class="footer"></div>' +
            '</div><br class="cb" /><br /><br />');
        contentArea.append(t);
        $('table', t).grid({
            source: data.rslt,
            columns: [
                { property: "name", label: "Name" },
                { property: "alias", label: "Alias" },
                { property: "term_group_alias", label: "Term Group Alias" }]
        }).grid('refresh');
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