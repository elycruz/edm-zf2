/**
 * @todo add comments
 * @todo implement a namespace function that modules can use to point or create
 * specific namespace
 */
define([
    'library/navigation/View',
    'library/data/Collection',
    'angular',
    'jquery'
], 

/**
 * ViewStack.js
 * @param edm/navigation/View View
 * @param edm/data/Collection Collection
 * @returns {_L16.ViewStack}
 */
function(View, Collection)
{
    function ViewStack(options) {
        View.apply(this);
        Collection.apply(this);
        this.itemConstructor = View;
        this.viewStackItemConstructor = ViewStack;
        if (!empty(options) && !empty(options.collection)) {
            this.addItems(options.collection);
            delete options.collection;
        }
        if (!empty(options)) {
            $.extend(this, options);
        }
    }

    ViewStack.fn = ViewStack.prototype;
    $.extend(ViewStack.fn, View.fn);
    $.extend(ViewStack.fn, Collection.fn);

    ViewStack.fn.$body = $('body').eq(0);

    ViewStack.fn.addItem = function(item) {
        var self = this;

        if (self.itemConstructor !== null && item
                instanceof self.itemConstructor === false &&
                empty(item.collection)) {
            self.collection.push(new self.itemConstructor(item));
        }
        else if (!empty(item.collection)) {
            self.collection.push(new self.viewStackItemConstructor(item));
        }
        else if (!empty(item)) {
            self.collection.push(item);
        }
    };

    ViewStack.fn.getViews = function() {
        return this.collection;
    };

    ViewStack.fn.appendViewItemTmpls = function(item) {
        var notAttached = item.tmplIds.filter(function(id) {
            return $('#' + id).length === 0;
        });
        if (notAttached.length > 0) {
            item.tmplIds.forEach(function(id) {
                $('#' + id).remove();
            });
            this.$body.append(item.tmpls);
        }
        item.tmplsAttached = true;
    };

    ViewStack.fn.loadViewItemTmpls = function(item) {
        var self = this;
        if (item.tmplsLoaded === true || empty(item.tmplsSrc)) {
            return;
        }
        require([item.tmplsSrc], function(txt) {
            item.tmpls = txt;
            self.appendViewItemTmpls(item);
            item.tmplsLoaded = true;
        });
    };

    ViewStack.fn.loadViewItemHtml = function(item) {
        var self = this;
        if (empty(item.htmlSrc)) {
            return;
        }
        require([item.htmlSrc], function(txt) {
            item.html = txt;
            self.$viewElement.html(txt);
        });
    };

    ViewStack.fn.loadViewItemEntrypoint = function(item) {
        if (empty(item.controller)) {
            return;
        }
        var self = this;

        require([item.controller], function(M) {
            if (!empty(M)) {
                M = new M();
                // Apply ko bindings if necessary
                if (item.bindToElm) {
                    ko.applyBindings(M, item.bindToElm);
                }
                // Init 
                if (M.hasOwnProperty('init')) {
                    M.init();
                }
            }
        }); // require entrypoint
    };

    ViewStack.fn.setCurrentView = function(item) {
        this.loadViewItemTmpls(item);
        this.loadViewItemHtml(item);
        this.loadViewItemEntrypoint(item);
    };

    return ViewStack;
}); 