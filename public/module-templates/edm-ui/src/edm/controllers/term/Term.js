define([
    'jquery',
    'knockout',
    'controllers/term/TermTable',
    'libraryview-models/paginator/Paginator',
    'librarydata/Collection'

            //    'view-models/HtmlElement'

], function($, ko, TermTable, Paginator) {

    var loc = window.location;

    return function Term() {
        var self = this,
                endpoint = loc.protocol + '//edmzf2/edm-admin',
                elm = $('#term-index');

        self.index = {
            page: 2,
            itemsPerPage: 3,
            items: null
        };
        
        self.index.action = function() {
            $.get(endpoint + '/term/index', {
                format: 'json',
                page: self.index.page,
                itemsPerPage: self.index.itemsPerPage
            }, function(data) {

                self.index.page = data.page;
                self.index.itemsPerPage = data.itemsPerPage;

                self.paginator = new Paginator({
                    items: {
                        perPage: self.index.itemsPerPage,
                        total: data.itemsTotal
                    },
                    pages: {
                        pointer: self.index.page,
                        total: data.itemsTotal / data.itemsPerPage
                    },
                    gotoPageNumCallback: function() {
                        trace(this.pages);
                    }
                });

                // Make data grid
                self.table = new TermTable({
                    data: data.results,
                    page: data.page,
                    itemsPerPage: data.itemsPerPage,
                    itemsTotal: data.itemsTotal
                });

                ko.applyBindings(self.table, $('.table-wrapper', elm).get(0));
                $('.paginator.controls', elm).each( function (i, elm) {
                    ko.applyBindings(self.paginator, elm);
                });
                
            }); // get

        };

        self.createAction = function() {
            var $elm = $('#term-create');
            $.get(endpoint + '/term/create', function(data) {
                $('.content', $elm).eq(0).append(data);
            });
        };

        self.updateAction = function() {
            $.get(endpoint + '/term/update', {
                format: 'html',
                alias: ''
            }, function(data) {
                trace(data);
            });
        };

        self.deleteAction = function() {
            $.get(endpoint + '/term/update', {
                format: 'html'
            }, function(data) {
                trace(data);
            });
        };

        self.init = function() {
            self.index.action();
            self.createAction();

            // Apply bindings
//            ko.applyBindings(self, $('#term').get(0));
        };

    } // Term

}); // define