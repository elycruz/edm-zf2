$(function()
{
    // Namespace
    var ns = {
        post: {
            category: {
                dialog: null
            },
            tag: {
                dialog: null
            }
        }
    },
    
    // Stage height
    SH = $(window).height(),
    
    // Stage width
    SW = $(window).width();
    
    function init() {
        // Initialize our edm object
        edm.location.useModule = true;
        edm.location.useController = true;
        edm.location.useAction = true;
        edm.init();

        // Only if current controller is not index
        if (edm.response.getControllerName() == 'index') {
            return;
        }      
        
        // Add jquery buttons
        $('input[type="submit"], input[type="reset"]').button({showText: true});    
        
        setupNavTop();
//        setupPostCategoryDialog();
//        setupPostIndexDialog();
        setupPostTagDialog();
    }

    function setupPostCategoryDialog() {
        
        // Post Category dialog
        $('a[href="/edm-admin/post-tag/index"]').each(function() {
            var o = $(this), 
            popup = $('<div title="Post tags" />');

            // Get create form
            $.get('/edm-admin/term-taxonomy/create/format/html/taxonomyLabel/' + 
                encodeURI('Post Tag') + 
                '/taxonomy/post-tag', function(data){
                popup.append(cleanData(data));
                
                ns.post.category.createDialog = normalizeAjaxForm(
                    popup.dialog({
                        width: 700, 
                        modal: true,
                        autoOpen: false
                    })
                );

                var parent_id = $('#parent_id').eq(0),//,
                    taxonomy = $('#taxonomy').eq(0);
                taxonomy.change(function(e) {
                    $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                            '/' + $(this).val() + '/format/json', function(data) {
                        setSelectOptions(parent_id, data.result);    
                    }); // ajax call
                }); // taxonomy change
                
                // Set parent_id
                $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                            '/post-category/format/json', function(data) {
                        setSelectOptions(parent_id, data.result);    
                }); // ajax call
                
                
            });

            // Click handler
            o.click(function(e) {
                e.preventDefault();
                ns.post.category.createDialog.dialog('open');
            });
            
        }); // setup post categories dialog
        
    } // set up post dialogs
    
    function setupPostTagDialog() {
        
        var tabs = $('<div id="post-tag-tabs"><ul class="menu">' +
            '<li><a href="#post-tag-index"><span>Tag index</span></a></li>' + 
            '<li><a href="#post-tag-create"><span>Add a tag</span></a></li>' +
            '</ul>' +
            '<div id="post-tag-index"></div>' +
            '<div id="post-tag-create"></div>' +
            '</div>').tabs(),
            postTagIndex = $('#post-tag-index', tabs),
            postTagCreate = $('#post-tag-create', tabs);
           
        function loadPostTagIndex() {
            // Get index
            $.get('/edm-admin/term-taxonomy/index/format/html/taxonomyFilter' + 
                '/tag', function(data){
                postTagIndex.html('');
                postTagIndex = normalizeDeleteAction(
                    normalizeAjaxLinks(
                        postTagIndex.append(cleanData(data))));
            });
        }
        
        // Load index
        loadPostTagIndex();

        // Get create form
        $('a[href="/edm-admin/post-tag/index"]').each(function() {
            var o = $(this); 

            // Get create form
            $.get('/edm-admin/term-taxonomy/create/format/html/taxonomyLabel/' + 
                encodeURI('Post Tag') + 
                '/taxonomy/tag', function(data){
                postTagCreate = normalizeAjaxForm(
                    postTagCreate.append(cleanData(data)), loadPostTagIndex);
                
            });
            

            ns.post.tag.dialog = $('<div id="post-tag-dialog" ' +
                'title="Post Tags" />').dialog({
                    autoOpen: false, 
                    width: 800,
                    modal: true 
                });
                
            // Append tabs
            ns.post.tag.dialog.append(tabs);
            
            // On tab change re-center dialog
            $('#post-tag-tabs').bind('tabsselect', function(e, ui){
                var dialog = ns.post.tag.dialog.parent();
                dialog.animate({marginTop: -(SH/2 - (dialog.height()/2))}, 300);
            });
            
            
            // Click handler
            o.click(function(e) {
                e.preventDefault();
                ns.post.tag.dialog.dialog('open');
            });

        }); // setup post categories dialog  
    }
    
    function setupPostIndexDialog() {
        // Post Category dialog
        $('a[href="/edm-admin/post-category/index"]').each(function() {
            var o = $(this), 
            popup = $('<div id="post-category-index" title="Post Category Index" />');

            // Get create form
            $.get('/edm-admin/term-taxonomy/index/format/html/taxonomyFilter' + 
                '/post-category', function(data){
                popup.append(cleanData(data));
                
                ns.post.category.indexDialog = 
                    normalizeDeleteAction(
                        normalizeAjaxLinks(
                            popup.dialog({
                                width: 800, 
                                modal: true,
                                autoOpen: false
                        })));
            });

            // Click handler
            o.click(function(e) {
                e.preventDefault();
                ns.post.category.indexDialog.dialog('open');
            });

        }); // setup post categories dialog
    }
    
    //------------------------------------------------
    // Utility functions
    //------------------------------------------------
    
    function cleanData(data) {
        data = $.trim(data);
        if (/^1/.test(data)) {
            data = data.replace(/^1/, '');
        }
        return data;
    }
    
    function normalizeAjaxForm(elm) {
        var callback = arguments[1] || null;
        $('form', elm).submit(function (e) {
            e.preventDefault();
            var form = $(this), formData = {},
            field, fieldName;
            
            $('input, select, textarea, file, submit, reset').each(function(){
                field = $(this);
                fieldName = field.attr('id') || field.attr('name');
                formData[fieldName] = field.val();
            });
            
            $('input[type="submit"], input[type="reset"]', $(this)).button({
                showText: true
            });
			
            $.post($(this).attr('action') + '/format/html', formData, 
            function (data) 
            {
                // Clean data of extraneous data
                data = cleanData(data);
                
                // Strip the extraneous one if it is there
                elm.html('');
                elm.html(data);
                elm = normalizeAjaxForm(elm, callback);
                
                // Callback
                if (callback) {
                    callback();
                }
                
            }); 
        });
        
        return elm;
    } // ajax elm post    
    
    function normalizeAjaxLinks(elm) {
        $('a[title!="delete"][title!="edit"]', elm).click(function(e){
            e.preventDefault();
            $.get($(this).attr('href'), function(data) {
                elm.html('');
                elm.html(cleanData(data));
                normalizeAjaxLinks(elm);
            });
        });
        return elm;
    }
    
    function normalizeDeleteAction(elm) {
        $('a[title="delete"]').click(function(e){
            e.preventDefault();
            
            var o = $(this), 
            row = o.parent().parent(),
            rowTitle = $('td', row).eq(1).html();

            $('<p>Are you sure you want to delete ' +
                'post item &quot;' + rowTitle + '&quot;?</p>').dialog({
                title: 'Post Delete Confirmation',
                resizable: false,
                height: 198,
                modal: true,
                buttons: {
                    "Delete item": function(e) {
                        $( this ).dialog( "close" );
                        $.get(o.attr('href'), function(data) {
                            elm.html('');
                            elm.html(normalizeDeleteAction(
                                normalizeAjaxLinks(cleanData(data))));
                        });
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            }); // dialog
            
        });
        return elm;
    }
    
    function normalizeEditAction(elm) {
        
    }
    
    function setSelectOptions(elm, options) {
        var selected = false, value;
        if (options.length > 0) {
            for (var item in options) {
                item = options[item];

                // Value
                value = item.term_alias;

                // is item selected
                if (elm.value == value) {
                    selected = true;
                }

                // Create new option
                elm.get(0).add(
                new Option(item.term_name, value, selected,selected));
                selected = false;
            }
        }
    }
        
    function setupNavTop() {
        // Drop down menus
        var nav_top = $( '#nav-top' );
        $( 'ul > li ul', nav_top ).each( function() {
            $(this).css('display','none');
            var p = $(this).parent();
            p.hover( function() {
                var ul = p.find( '> ul' );
                ul.slideDown( 'fast' ).show();
            },
            function() {
                var ul = p.find( '> ul' );
                            ul.slideUp( 'fast' );
            });
        });
    }
    
    // Initialize
    init();
    
});