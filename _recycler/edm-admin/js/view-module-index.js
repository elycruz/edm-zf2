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
    function initPaginationControls() {
        
    }

    function initRowControls()
    {
        // List order elements
        $('.list-order-control').each(function() {
            var o = $(this), 
                id = $('input', o).eq(0).attr('id').split('_')[1], 
                url;
            o.listOrderControl({setListOrderCallback: function(data) {
                url = '/edm-admin/view-module/set-list-order/id/' + id + 
                    '/listOrder/' + data.listOrder + '/format/json';
                $.get(url, function(data2) {
                    trace(var_dump(data2));
                });
            }});
        });

        // View Module's Term Taxonomy Id
        $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                '/position/format/json', function(data) {
            $('.term-taxonomy-control').each(function() {
                var o = $(this), id = o.parent().attr('id').split('--')[1];
                o.termTaxonomyControl({
                    controls: {
                        select: {
                            value: $.trim(o.text()),
                            htmlId: 'term-taxonomy-control--' + id,
                            htmlClass: 'term-taxonomy-select',
                            options: data.result,
                            optionValueColumnName: 'term_taxonomy_id'
                        }
                    },
                    onChangeCallback: function(data) {
                        $.get('/edm-admin/view-module/set-term-taxonomy/format/json' +
                            '/id/' + id +
                            '/term_taxonomy_id/' + data.value, function(data2) {
                                trace(var_dump(data2));
                            });
                    }
                });
            });
        });

        // View Module Status
        $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                '/post-status/format/json', function(data) {
            $('.status-control').each(function() {
                var o = $(this), id = o.parent().attr('id').split('--')[1];
                o.termTaxonomyControl({
                    controls: {
                        select: {
                            value: $.trim(o.text()),
                            htmlId: 'status-control--' + id,
                            htmlClass: 'status-select',
                            options: data.result,
                            optionValueColumnName: 'term_alias'
                        }
                    },
                    onChangeCallback: function(data) {
                        $.get('/edm-admin/view-module/set-status/format/json' +
                            '/id/' + id +
                            '/status/' + data.value, function(data2) {
                                trace(var_dump(data2));
                            });
                    }
                });
            });
        });
        
        // Post Delete
        $('#content-area table a[title="delete"]').each(function(){
            var o = $(this);
            o.click(function(e){
                var row = o.parent().parent(),
                rowTitle = $('td', row).eq(1).html();
                
                e.preventDefault();
                $('<p>Are you sure you want to delete ' +
                    'post item &quot;' + rowTitle + '&quot;?</p>').dialog({
                    title: 'Post Delete Confirmation',
                    resizable: false,
                    height: 198,
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
    } // init row controls
    
    function init() {
        initRowControls();
    }
    
    function getRowId(row) {
        return row.attr('id').split('--')[1];
    }
    
    function generateTableRow(jsonTuple) {
        var o = jsonTuple;
//        <tr id="row-id--00" class="odd">
//            <td>1.</td>
//            <td><a href="/edm-admin/view-module/update/id/23" class="title font-georgia">title</a></td>
//            <td>post</td>
//            <td class="term-taxonomy-control"></td>
//            <td class="status-control"></td>
//            <td class="list-order-control">
//                <a class="next-btn" href="/edm-admin/view-module/set-list-order/id/23/format/json/listOrder/24">
//                    <span>Up</span></a> |
//                <a class="prev-btn" href="/edm-admin/view-module/set-list-order/id/23/format/json/listOrder/26">
//                    <span>Down</span></a> |
//                <input type="text" class="text-field" id="listOrder_23" name="listOrder_23" value="25" size="4" maxlength="20">    </td>
//            <td class="last-updated"><span class="font-georgia">3/15/2012</span><br><span class="tsml">7:26 PM</span>     </td>
//            <td class="created"><span class="font-georgia">4/3/2012</span><br><span class="tsml">3:49 PM</span> </td>
//            <td class="crud-control"><a href="/edm-admin/view-module/update/id/23" title="edit">edit</a>|
//                    <a href="/edm-admin/view-module/delete/id/23" title="delete">delete</a>    
//            </td>
//        </tr>
    }
    
    init();
    
});