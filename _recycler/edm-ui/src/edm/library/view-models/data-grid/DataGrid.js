define([
    'knockout', 
    'library/ui/HtmlElement',
    'library/view-models/helper/TemplateLoadHelper',
    'library/data/Collection',
    'text!library/view-models/data-grid/data-grid-tmpls.html'
    ], 
    function (ko, HtmlElement, TemplateLoadHelper, Collection) {
    
        'use strict';
    
        function DataGridCell (options) {
            this.visible = ko.observable(true);
            this.alias = 'column-alias';
            this.value = 'Sample value';
            if (options.visible) {
                this.visible(options.visible);
                delete options.visible;
            }
            HtmlElement.apply(this, [options]);
        }
    
        DataGridCell.fn = 
        DataGridCell.prototype = 
        Object.create(HtmlElement.fn);
    
        DataGridCell.fn.hide = function () {
            this.visible(false);
        };
    
        DataGridCell.fn.show = function () {
            this.visible(true);
        };
    
        // -------------------------------------------
    
        function DataGridRow (options) {
            var self = this,
            emptyOptions = empty(options);
            if (!emptyOptions && !empty(options.attribs)) {
                HtmlElement.apply(self, [{
                    attribs: options.attribs
                }]);
            }
            else {
                HtmlElement.apply(self);
            }
        }
        
        // -------------------------------------------
    
        function DataGridColumn (data) {
            this.label = 'Label';
            this.childCall = null;
            DataGridCell.apply(this, [data]);
        }
    
        DataGridColumn.fn = 
        DataGridColumn.prototype =
        Object.create(DataGridCell.fn);
    
        DataGridColumn.fn.getChildCellAttribs = function () {
            return this.childAttribs ? this.childAttribs : {};
        };
    
        // -------------------------------------------
    
        /**
         * Data Grid View Model
         * @param options object default null
         */
        function DataGrid (options) {
            var self = this,
            emptyOptions;
            self.rowConstructor = DataGridRow;
            self.columnConstructor = DataGridColumn;
            
            //            self.rows = new Collection({
            //                itemConstructor: DataGridRow
            //            });
            //            self.columns = new Collection({
            //            });
            
            self.rows = ko.observableArray();
            self.columns = ko.observableArray();
            self.tbodyTmpl = 'tbody-tmpl';
            self.headerText = 'Sample header text';
            self.footer = 'Sample footer';
            
            emptyOptions = empty(options);
        
            // Add data if necessary
            if (!emptyOptions && !empty(options.data)) {
                options.data.forEach(function (x) {
                    self.rows.push(new DataGridRow(x));
                });
                delete options.data;
            }
        
            if (!emptyOptions && !empty(options.rows)) {
                options.rows.forEach(function (x) {
                    self.rows.push(new DataGridRow(x));
                });
                delete options.rows;
            }
        
            // Add columns if necessary
            if (!emptyOptions && !empty(options.columns)) {
                options.columns.forEach(function (x) {
                    self.columns.push(new DataGridColumn(x));
                });
                delete options.columns;
            }
            
            // Set attributes
            if (!emptyOptions && !empty(options.attribs)) {
                HtmlElement.call(self, {
                    attribs: options.attribs
                });
                delete options.attribs;
            }
            else {
                HtmlElement.apply(self);
            }

            $.extend(self, options)
            if (self.tmplsLoaded === false) {
                self.loadTmplsFromConfig(self.tmplsSrcConfig);
            }
            
            this.init = function () {};
        }
    
        DataGrid.fn = 
        DataGrid.prototype =
        new HtmlElement();
    
        DataGrid.fn.tmplsLoaded = false;

        DataGrid.fn.tmplsSrcConfig = {
            tmplsSrc: 'text!library/view-models/data-grid/data-grid-tmpls.html',
            ids: [
            'table-tmpl',
            'thead-headerText-tmpl',
            'thead-columns-tmpl',
            'tbody-tmpl'
            ]
        };
                
        DataGrid.fn.toggleColumn = function (index) {
            this.columns[index].hide();
        };
    
        DataGrid.fn.setHeaderText = function (str) {
            this.headerText(str);
        };
    
        DataGrid.fn.loadTmplsFromConfig = function (config) {
            config = config || DataGrid.fn.tmplsSrcConfig;
            TemplateLoadHelper.fn.loadFromConfig(config, DataGrid, this, 'init');
        };
    
        return DataGrid;
    
    }); // define