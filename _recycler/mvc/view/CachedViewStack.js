define([
    'view-models/mvc/view/ViewStack',
    'view-models/CachedView'
], 
    function (ViewStack, CachedView) {
        
        /**
         * Cached View Stack
         * @param options
         * @return default
         */
        function CachedViewStack (options) 
        {
            this.viewConstructor = CachedView;
            ViewStack.apply(this, options);
        }

        // Alias prototype
        CachedViewStack.fn = CachedViewStack.prototype;
        
        // Exetnd CachedViewStack's prototype with ViewStack's prototype
        $.extend(true, CachedViewStack.fn, ViewStack.prototype);
        
        return CachedViewStack;
    });