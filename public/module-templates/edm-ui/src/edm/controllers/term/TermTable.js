define([
    'libraryview-models/data-grid/DataGrid'
],
        function(DataGrid) {
            'use strict';
            function TermTable(options) {

                var self = this,
                        tableId = 'term-index:table';

                self.options = {
                    attribs: {
                        id: tableId,
                        'class': 'data-grid form'
                    },
                    headerText: 'Term  Index',
                    rowId: function(d, i) {
                        return tableId
                                + ':rowIndex:'
                                + (((self.page - 1) * self.itemsPerPage) + i())
                                + ':rowId:' + d.alias;
                    },
                    rowOnClick: function() {

                    },
                    columns: [
                        {
                            value: 'No.',
                            alias: 'indexLabel',
                            childCall: function(d, i) {
                                var index = ((self.page - 1) * self.itemsPerPage) + i(),
                                        nm = tableId
                                        + ':checkbox:' + index
                                        + ':rowId:' + d.alias;
                                return '<input type="checkbox" '
                                        + 'class="row-select-box" '
                                        + 'id="' + nm + '"'
                                        + 'name="' + nm + '" />'
                                        + '<label for="' + nm + '">'
                                        + (index + 1) + ').'
                                        + '</label>';
                            }
                        },
                        {
                            value: 'Name',
                            alias: 'name',
                            attribs: {
                                width: '30%'
                            }
                        },
                        {
                            value: 'Alias',
                            alias: 'alias',
                            attribs: {
                                width: '30%'
                            }
                        },
                        {
                            value: 'Term Group Alias',
                            alias: 'term_group_alias',
                            attribs: {
                                width: '20%'
                            }
                        },
                        {
                            value: 'Options',
                            alias: '',
                            childAttribs: {
                                'class': 'controls'
                            },
                            childCall: function(d) {
                                return '<a class="btn" href="/edm-admin/term/update/'
                                        + d.alias + '">edit</a>'
                                        + '<a class="btn" href="/edm-admin/term/delete/'
                                        + d.alias + '">delete</a>';
                            }
                        }]
                };

                if (!empty(options)) {
                    $.extend(true, self.options, options);
                }

                DataGrid.apply(self, [self.options]);
                return this;
            }

            TermTable.fn =
                    TermTable.prototype =
                    new DataGrid();

            return TermTable;

        });
