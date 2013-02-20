define([
    
    'jquery', 
    'knockout',
    'libraryview-models/SimpleTabs',
    'controllers/term-taxonomy/TermTaxonomyTable'

    ], function ($, ko, SimpleTabs, TermTaxonomyTable) {
        
        var loc = window.location;
    
        function TermTaxonomy () 
        {
            var self = this,
            
            // Endpoint
            endpoint = loc.protocol +'//'+ loc.host + '/edm-admin';
        
            self.elm = $('#term-taxonomy-index');
            
            self.tabs = null;
            
            self.indexTable = null;
        
            self.indexAction = function () {
                $.get(endpoint + '/term-taxonomy/index', {
                    format: 'json'
                }, function (data) {
//                    self.indexTable.rows.addItems(data.result);
                    trace(data);
                    
                }); // get
            }; // index
        
            self.createAction = function () {
                // Get and display form
                $.get(endpoint + '/term-taxonomy/create', {
                    format: 'html'
                }, function (data) {
                    $('> .body > .content', self.elm).append(data);
                });
            };
        
            self.updateAction = function () {
                $.get(endpoint + '/term-taxonomy/update', {
                    format: 'html'
                }, function (data) {
                    trace(data);
                });
            };
        
            self.deleteAction = function () {
                $.get(endpoint + '/term/update', {
                    format: 'html'
                }, function (data) {
                    trace(data);
                });
            };
        
            self.init = function () {
                // Term taxonomy table
                self.indexTable = self.getIndexTable();
                
                // Apply bindings
                ko.applyBindings(self.indexTable, $('#term-taxonomy-index').get(0));
                
                // Dispatch controller action
                self.indexAction();
            };
            
            self.getIndexTable = function () {
                if (empty(self.indexTable)) {
                    $.get('/term', function (data) {
                        self.indexTable = new TermTaxonomyTable({data: data.results});
                    });
                }
                return self.indexTable;
            };
            
            self.getTabs = function () {
                if (empty(self.tabs)) {
                    self.tabs = new SimpleTabs();
                }
                return self.tabs;
            };
        
        }; // Term Taxonomy
        
        return TermTaxonomy;
    
    }); // define