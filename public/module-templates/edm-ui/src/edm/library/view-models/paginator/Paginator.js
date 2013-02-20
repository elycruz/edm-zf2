define(['knockout', 'library/ui/HtmlElement'],
    function(ko, HtmlElement) {

        function Paginator(options) {
            var self = this;
            self.options = {
                attribs: {},
                // Integers
                items: {
                    firstInRange: 0,
                    lastInRange: 0,
                    perPage: 0
                },
                // Integers
                pages: {
                    pointer: 0,
                    total: 0,
                    direction: 1
                },
                firstBtn: {
                    text: 'first',
                    enabled: true,
                    attribs: {
                        disabled: true,
                        'class': 'btn'
                    }
                },
                prevBtn: {
                    text: 'previous',
                    enabled: true,
                    attribs: {
                        disabled: false,
                        'class': 'btn'
                    }
                },
                textField: {
                    enabled: true,
                    attribs: {
                        disabled: false,
                        'class': 'text-field',
                        value: 1
                    }
                },
                nextBtn: {
                    text: 'next',
                    enabled: true,
                    attribs: {
                        disabled: false,
                        'class': 'btn'
                    }
                },
                lastBtn: {
                    text: 'last',
                    enabled: true,
                    attribs: {
                        disabled: false,
                        'class': 'btn'
                    }
                },
                gotoPageNumCallback: null
            }; // self . options

            self.refreshFirstAndLastInRange = function() {
                var pages = self.pages,
                items = self.items;

                // Set first and last indices in range
                if (items.perPage > 1) {
                    items.firstInRange = items.perPage * pages.pointer -
                    (items.perPage - 1);
                    items.lastInRange = items.perPage * pages.pointer;
                }
                else {
                    items.firstInRange =
                    items.lastInRange = 1;
                }
            };

            self.nextPage = function() {
                // Set direction to next
                self.pages.pointer_direction = 1;
                if (self.pages.pointer <
                    self.pages.total - 1) {
                    self.pages.pointer += 1;
                }
                else {
                    self.pages.pointer = 0;
                }

                // Goto Page src number
                self.setPointer(self.pages.pointer);
            };

            self.prevPage = function() {
                if (self.pages.pointer > 0) {
                    self.pages.pointer -= 1;
                }
                else {
                    self.pages.pointer =
                    self.pages.total - 1;
                }

                // Set direction to previous
                self.pages.pointer_direction = -1;
                // Goto Page src number
                self.setPointer(self.pages.pointer);
            };

            self.gotoPageNum = function(num) {
                // Set prev and next
                self.pages.prev = num - 1;
                self.pages.next = num + 1;

                // Refresh first and last index integers
                self.refreshFirstAndLastInRange();

                // Set pointer
                self.pages.pointer = 
                    self.pointer(num).pointer();

                // Make callback
                if (typeof self.gotoPageNumCallback === 'function') {
                    self.gotoPageNumCallback({
                        pointer: self.pages.pointer,
                        itemsPerPage: self.items.perPage
                    });
                }
            };

            self.onFirstBtnClick = function() {
                self.setPointer(0);
                if (!empty(self.firstBtn.callback)) {
                    self.firstBtn.callback();
                }
            };

            self.onPrevBtnClick = function() {
                self.prevPage();
                if (!empty(self.prevBtn.callback)) {
                    self.prevBtn.callback();
                }
            };

            self.onNextBtnClick = function() {
                self.nextPage();
                if (!empty(self.nextBtn.callback)) {
                    self.nextBtn.callback();
                }
            };

            self.onLastBtnClick = function() {
                self.gotoPageNum(self.pages.total - 1);
                if (!empty(self.lastBtn.callback)) {
                    self.lastBtn.callback();
                }
            };

            self.onTextFieldKeyUp = function(d, e) {
                var outgoing = {};
                // If the enter key was not pressed bail
                if (e.keyCode != 13) {
                    return;
                }

                // Prelims
                var field = $(this), value = field.val();
                if (/\d+/.test(value)) {
                    // goto page number
                    if ((value - 1) > self.pages.total) {
                        alert('Range Exception: Paginator value entered is ' +
                            'out of range.  Value entered: ' + value + '\n\n' +
                            'proceeding to last page.');
                        // Proceed to greates page number
                        self.setPointer(self.pages.total - 1);
                    }
                    else if ((value - 1) < 0) {
                        alert('Range Exception: Paginator value entered is ' +
                            'out of range.  Value entered: ' + value + '\n\n' +
                            'Proceeding to first page.');
                        // Proceed to first page
                        self.setPointer(0);
                    }
                    else {
                        // Proceed to passed in page number
                        self.setPointer(value - 1);
                    }

                }
                else {
                    outgoing.messages = ['Only numbers are allowed in the ' +
                    'paginator textfield.'];
                }

                if (typeof self.textField.callback === 'function') {
                    // Set up some outgoing data for callbacks
                    outgoing.items = self.items;
                    outgoing.pages = self.pages;
                    self.textField.callback(outgoing);
                }
            };

            // Init observables 
            self.setOptions = ko.computed({
                read: function () {
                    return null;
                },
                write: function (options) {
                    $.extend(true, self, options);
                    if (isset(options.pointer)) {
                        self.setPointer(self.pages.pointer);
                    }
                }
            });
                
            self.init = function() {
                if (!empty(options)) {
                    $.extend(true, self.options, options);
                }
                $.extend(self, self.options);
                if (!empty(self.options.attribs)) {
                    HtmlElement.apply(self, {
                        attribs: self.options.attribs
                    });
                }
                else {
                    HtmlElement.apply(self);
                }
                delete self.options;
                
                self.setPointer = ko.computed({
                    read: function () {
                        return Number(self.pages.pointer);
                    },
                    write: function (val) {
                        self.pages.pointer = Number(val);
                        self.gotoPageNum(self.pages.pointer);
                    }
                });
                
                self.pointer = ko.observable(self.pages.pointer);
                self.itemsPerPage = ko.observable(self.items.perPage);
                self.totalItems = ko.observable(self.items.total);
                self.totalPages = ko.observable(self.pages.total);
                self.firstItemInRange = ko.observable(self.items.firstInRange);
                self.lastItemInRange = ko.observable(self.items.lastInRange);
            };

            self.init();
        }

        Paginator.fn =
        Paginator.prototype =
        new HtmlElement();

        return Paginator;

    });

