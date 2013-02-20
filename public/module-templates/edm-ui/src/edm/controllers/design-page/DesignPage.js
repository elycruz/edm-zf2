define([
    'knockout',
    'libraryui/navigation/ViewStack',
    'libraryview-models/data-grid/DataGrid', 
    //    'view-models/simple-tabs/SimpleTabs',
    'es5', 
    'phpjs', 
    'jquery', 
    'jquery-ui'
    ],

    function(ko, ViewStack, DataGridModel) {
        'use strict';

        /**
         * Test module for design page
         * @return default
         */ 
        function DesignPage() 
        {
            // Test data grid model
            var contentViewStack, 
            systemViewStack,
            viewModuleViewStack,
            leftCol = $('#left'),
            mainView = $('#main'),
            postsTestElm = $('#items-test'),
            dataGrid = new DataGridModel({
                headerText: 'Table 1 header text',
                columns: [
                {
                    label: 'Column 1',
                    alias: 'column1'
                },
                {
                    label: 'Column 2',
                    alias: 'column2'
                },
                {
                    label: 'Column 3',
                    alias: 'column3'
                },
                {
                    label: 'Column 4',
                    alias: 'column4'
                }],
                data: [
                {
                    column1: 'value 1',
                    column2: 'value 2',
                    column3: 'value 3',
                    column4: 'value 4'
                },
                {
                    column1: 'value 1',
                    column2: 'value 2',
                    column3: 'value 3',
                    column4: 'value 4'
                },
                {
                    column1: 'value 1',
                    column2: 'value 2',
                    column3: 'value 3',
                    column4: 'value 4'
                }]
            });
            
            // Apply ko bindings
            //ko.applyBindings(dataGrid, $('#ko-data-grid-test').get(0));
        
            // Test some jquery ui components
            
            // Range field
            //            $('#range-field').replaceWith('<div id="range-field" class="m10px" />');
            //            $('#range-field').slider();
            
            // Number field
            //            $('#number-field').replaceWith('<div id="number-field" name="number-field" class="m10px" />');
            //            $('#number-field').spinner();
            
            // Date field
            //            $('#date-field').replaceWith('<input type="text" id="date-field" class="m10px" />');
            //            $('#date-field').datepicker();
            
            /**
            * Left column content menu optoins
            * @var view-models/mvc/view/ViewStack
            */ 
            contentViewStack = new ViewStack({
                title: 'Content sub menu',
                ulAttribs: {
                    'class': 'vert-menu-left menu'
                },
                collection: [
                {
                    route: '#!/post',
                    htmlSrc: 'text!view-template/post/index.html',
                    attribs: {
                        'class': 'first' 
                    },
                    label: 'Posts',
                    clickFunction: 'changeCurrentView'
                },
                {
                    route: '#!/category',
                    htmlSrc: 'text!view-template/post-category/index.html',
                    label: 'Categories',
                    clickFunction: 'changeCurrentView'
                },
                {
                    route: '#!/tag',
                    htmlSrc: 'text!view-template/post-tag/index.html',
                    label: 'Tags',
                    clickFunction: 'changeCurrentView'
                },
                {
                    route: '#!/media',
                    htmlSrc: 'text!view-template/post-media/index.html',
                    attribs: {
                        'class': 'last' 
                    },
                    label: 'Media',
                    clickFunction: 'changeCurrentView'
                }],
                $viewElement: mainView
            });
        
            /**
            * System sub menu options
            * @var view-models/mvc/view/ViewStack
            */ 
            systemViewStack = new ViewStack({
                title: 'System sub menu',
                ulAttribs: {
                    'class': 'vert-menu-left menu'
                },
                collection: [
                {
                    route: '#!/term',
                    htmlSrc: 'text!view-templates/term/index.html',
                    tmplsSrc: 'text!view-templates/term/tmpls.html',
                    controller: 'controllers/term/Term',
                    tmplIds: [
//                    'table-tmpl',
//                    'thead-header-txt-tmpl',
//                    'thead-columns-tmpl',
//                    'tbody-tmpl'
                    ],
                    attribs: {
                        'class': 'first' 
                    },
                    label: 'Terms',
                    clickFunction: 'changeCurrentView'
                },
                {
                    route: '#!/term-taxonomy',
                    htmlSrc: 'text!controllers/term-taxonomy/index.html',
                    controller: 'controllers/term-taxonomy/TermTaxonomy',
                    label: 'Term Taxonomies',
                    clickFunction: 'changeCurrentView'
                },
                {
                    route: '#!/user',
                    htmlSrc: 'text!view-template/user/index.html',
                    attribs: {
                        'class': 'first' 
                    },
                    label: 'Users',
                    clickFunction: 'changeCurrentView'
                }],
                $viewElement: mainView
            });
        
            /**
            * View Module sub menu options
            * @var view-models/mvc/view/ViewStack
            */ 
            viewModuleViewStack = new ViewStack({
                title: 'View Module sub menu',
                ulAttribs: {
                    'class': 'vert-menu-left menu'
                },
                collection: [
                {
                    route: '#!/view-module',
                    htmlSrc: 'text!view-template/view-module/index.html',
                    attribs: {
                        'class': 'first' 
                    },
                    label: 'View Modules'
                },
                {
                    route: '#!/menu',
                    htmlSrc: 'text!view-template/view-module/menu/index.html',
                    label: 'Menu'
                },
                {
                    route: '#!/plain-html',
                    htmlSrc: 'text!view-template/view-module/plain-html/index.html',
                    attribs: {
                        'class': 'first' 
                    },
                    label: 'Plain html'
                }],
                $viewElement: mainView
            });
        
            // Apply ko bindings
            ko.applyBindings(contentViewStack, $('#content-sub-menu').get(0));
            ko.applyBindings(systemViewStack, $('#system-sub-menu').get(0));
            ko.applyBindings(viewModuleViewStack, $('#view-module-sub-menu').get(0));
    
            // Make module headers toggle their bodies
//            $('#main > .module > header').on('click', function(e) {
//                $('> div', $(this).parent()).toggle(300);
//            });
            
            // H3 left col click
            $('h3', leftCol).click(function (e) {
                var elm = $(this);
                trace(elm.get(0).nodeName);
                $('> div', elm.parent().parent()).eq(0).toggle('fast');
            });
        
            // Prelims
            var windowW = $(window).width(),
            leftColHandle = $('> .handle a', leftCol),
            leftColModules = $('.module', leftCol);

            // Left col handle click
            leftColHandle.click(function () {
                var shouldBe = windowW * 0.23;
                if (leftCol.width() < shouldBe) {
                    mainView.css({
                        left: '23%', 
                        width: '75%'
                    });
                    leftCol.css({
                        minWidth: '220px', 
                        width: '23%'
                    });
                    leftColModules.toggle(160);
                }
                else {
                    leftCol.css({
                        minWidth: 0, 
                        width: 0
                    });
                    leftColModules.toggle(160);
                    mainView.css({
                        width: '96%', 
                        left: 0
                    });
                }
            });

            // On window resize
            $(window).smartResize(function () {
                windowW = $(this).width();
            });
            
        
        } // Design Page
        
        return DesignPage;
        
    }); // define