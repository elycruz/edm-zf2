define(['view-models/Stack'], function (Stack) 
{
    function Tab(options) {
        var self = this;
        self.label = 'Tab label';
        self.id = 'tab-id';
        $.extend(self, options);
    }

    function SimpleTabs (options) {
        var self = this;
        self.itemConstructor = Tab;
        Stack.apply(self, options);
    }

    SimpleTabs.fn = SimpleTabs.prototype;
    
    SimpleTabs.fn.addTab = function (tab) {
        var self = this;
        self.addItem(tab);
    };
    
    SimpleTabs.fn.changeTab = function (data, e) {
        var self = this;
        self.tabs.each(function () {
            var tab = $(this);
            tab.hide();
            if (tab.attr('id') === data.href) {
                tab.show();
            }
        });
    };
    
    return SimpleTabs;
});