/**
 * @todo add comments
 * @todo implement a namespace function that modules can use to point or create
 * specific namespace
 */
define(['knockout', 'view-models/mvc/view/View'], function(ko, View) 
{
    function ViewStack (data) 
    {
        var self = this,
        
        views;
       
        self.views = null;
        
        self.viewConstructor = View;
        
        self.viewStackConstructor = ViewStack;
        
        self.viewElm = null;
        
        self.changeCurrView = function (data, e) {
            var viewItem = data,
            viewElm = self.viewElm;
            viewElm.fadeOut(300, function () {
            self.processViewChange(viewItem, viewElm);
                $(this).fadeIn(300);
            });
        };
        
        self.sayHello = function (data, e) {
            trace('sayhello viewstack');
        };
        
        if (empty(data)) {
            return;
        }
        
        if (!empty(data.views)) {
            self.views = [];
            views = data.views;
            self.addViews(views);
            delete data.views;
        }
        
        $.extend(self, data);
    }
    
    ViewStack.fn = ViewStack.prototype;
    
    ViewStack.fn.addViews = function (views) {
        var self = this, view;
        
        views = typeof views === 'function' ? views() : views;
        
        if (!Array.isArray(views)) {
            throw {
                message: 'ViewStack.addViews requires an array ' +
            'or an observable array.'
            };
        }
        
        views.forEach(function(view, i) {
            // Set view list order
            if (empty(view.listOrder)) {
                view.listOrder = i;
            }
            
            // Add view item
            self.addView(view);
        });
    };
    
    ViewStack.fn.addView = function (view, constructor, stackConstructor) {
        var self = this,
        Construct = constructor || self.viewConstructor || View,
        StackConstruct = stackConstructor || 
            self.viewStackConstructor || ViewStack;
        
        if (!empty(view.views)) {
            view.stackModel = new StackConstruct(view);
        }
        
        view = new Construct(view);
        self.views.push(view);
    };
    
    ViewStack.fn.getViews = function () {
        return this.views;
    };
    
    /**
     * Returns an empty object or ul attributes if specified
     * @return mixed object 
     */
    ViewStack.fn.getAttribs = function () {
        var retVal = {}, self = this;
        
        if (!empty(self.ulAttribs )) {
            retVal = self.ulAttribs;
        }
        else if (!empty(self.attribs)) {
            retVal = self.attribs;
        }
        
        return retVal;
    };
    
    ViewStack.fn.processViewChange = function (viewItem, viewElm) {

        // Check if template is available
        // @todo change template to markup or something more semantically correct
        if (empty(viewItem.htmlSrc)) {
            return;
        }
        
        // Load view template/markup
        require([viewItem.htmlSrc], function (tmpl) {
            viewElm.html(tmpl);
        });
        
        // If module
        if (!empty(viewItem.module)) {
            
            require([viewItem.module], function(M) {
                M = new M();
                
                // Init 
                if (M.hasOwnProperty('init')) {
                    M.init();
                }
                
                // Apply ko bindings if necessary
                if (viewItem.bindToElm) {
                    ko.applyBindings(M, viewItem.bindToElm);
                }
            });
        }
        
        // If templates source
        if (!empty(viewItem.tmplsSrc) && empty(viewItem.tmplsLoaded)) {
            require([viewItem.tmplsSrc], function (tmpls) {
                viewItem.tmplsLoaded = true;
                $('body').append(tmpls);
            });
        }
    };
    
    return ViewStack;
});