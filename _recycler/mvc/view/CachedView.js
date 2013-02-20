/**
 * Think about the zend framework paradigm of doing mvc
 * @todo think about how it composes view stacks or has pointers to them and 
 * how it has a view renderer 
 * @todo think about have CachedView objects fetch their own source 
 */
// @todo add either a clean up method and maybe a render method
define(['view-models/mvc/view/View'], function (View) {
    function CachedView (options) {
        
        /**
         * Help nested calls of this
         * @var CachedView
         */
        var self = this;
                
        /**
         * Auto init flag
         * @var boolean default false
         */
        self.autoInit = false;
        
        // Inherit self properties from View
        View.apply(self, options);
        
        // Call init
        if (self.autoInit) {
            self.init();
        }
    }
    
    /**
     * Alias for CachedView.prototype
     * @var object default prototype
     */
    CachedView.fn = CachedView.prototype;
    
    
    // Inherit/Copy-over View Stack Item's prototype methods 
    $.extend(CachedView.fn, View.fn);
    
    /**
     * Initializes view and loads any resources into the view object the view
     * may have
     * @return default
     */
    CachedView.fn.init = function () {
        
        // Segregate this
        var self = this;
        
        // Load html source if any
        if (self.htmlSrc) {
            require([self.htmlSrc], function (txt) {
                self.html = txt;
                
                // @todo either trigger an event or call a callback here
            });
        }
        
        // Load templates source if any
        if (self.tmplsSrc) {
            require([self.tmplsSrc], function (txt) {
                self.tmpls = txt;
            });
        }
    }; // init
    
    /**
     * Lazy loads the html string associated with this view 
     * @return string
     */
    CachedView.fn.getHtml = function () {
        var self = this;
        // Load html source if necessary
        if (empty(self.html) && !empty(self.htmlSrc)) {
            require([self.htmlSrc], function (txt) {
                self.html = txt;
                // @todo either trigger an event or call a callback here
            });
        }
        // Return html string
        return self.html;
    };
    
    /**
     * Lazy loads the tmpls string associated with this view
     * @return string
     */
    CachedView.fn.getTmpls = function () {
        var self = this;
        // Load tmpls source if necessary
        if (empty(self.tmpls) && !empty(self.tmplsSrc)) {
            require([self.tmplsSrc], function (txt) {
                self.tmpls = txt;
                // @todo either trigger an event or call a callback here
            });
        }
        // Return tmpls string
        return self.tmpls;
    };
    
    // Return view object
    return CachedView;
   
});