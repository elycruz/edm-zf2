define(['knockout', 'library/data/Collection'], 
function (ko, Collection) 
{
    'use strict';
    
    function Tab(options) {
        var self = this;
        self.label = 'Tab label';
        self.id = 'tab-id';
        $.extend(self, options);
    }

    function SimpleTabs (options) {
        var self = this;
        Collection.apply(self, options);
        
        self.itemConstructor = Tab;
        
        var emptyOptions = empty(options);
        if (!emptyOptions && !empty(options.items)) {
            self.addTabs(options.items);
        }
    }

    SimpleTabs.fn = SimpleTabs.prototype;
    
    $.extend(SimpleTabs.fn, Collection.fn);
    
    SimpleTabs.fn.addTab = function (tab) {
        this.addItem(tab);
    };
    
    SimpleTabs.fn.addTabs = function (tabs) {
        this.addItems(tabs);
    };
    
    SimpleTabs.fn.changeTab = function (data, e) {
        var self = this;
        self.items.forEach(function (tab) {
            tab.hide();
            if (tab.attr('id') === data.href) {
                tab.show();
            }
        });
    };
    
    return SimpleTabs;
});