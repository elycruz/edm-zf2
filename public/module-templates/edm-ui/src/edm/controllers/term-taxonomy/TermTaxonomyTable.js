define([
    'libraryview-models/data-grid/DataGrid'
    ],
    function (DataGrid) {
    
        function TermTaxonomyTable (options) {
        
            var self = this;
                
            self.options = {
            
                headerText: 'Term Taxonomy Index',
            
                columns: [
                {
                    label: 'No.',
                    alias: 'indexLabel'
                },
                {
                    label: 'Name',
                    alias: 'term_name'
                },
                {
                    label: 'Alias',
                    alias: 'term_alias'
                },
                {
                    label: 'Taxonomy',
                    alias: 'taxonomy'
                },
                {
                    label: 'List order',
                    alias: 'listOrder'
                },
                {
                    label: 'Options',
                    alias: ''
                }],
        
                rows: null
            
            };

            if (!empty(options)) {
                $.extend(self.options, options);
            }
        
            options = self.options;
            
            DataGrid.apply(self);
        
            var emptyOptions = empty(options);
        
            // Add data if necessary
            if (!emptyOptions && !empty(options.data)) {
                self.rows.addItems(options.data);
            }
            delete options.data;
        
            if (!emptyOptions && !empty(options.rows)) {
                self.rows.addItems(options.rows);
            }
            delete options.rows;
        
            // Add columns if necessary
            if (!emptyOptions && !empty(options.columns)) {
                self.columns.addItems(options.columns);
            }
            delete options.columns;

            // Extend this with options from outside
            if (!emptyOptions) {
                $.extend(self, options);
            }
        }
    
        TermTaxonomyTable.prototype = new DataGrid();
    
        return TermTaxonomyTable;

    });
