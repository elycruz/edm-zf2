define(function () {
    function Service() {
        var self = this;
        
        /**
         * Url endpoint
         * @var string default null
         */
        self.endpoint = null;

        /**
         * Endpoint params
         * @var object default null
         */
        self.urlParams = null; 
    }
    
    // Alias for term taxonomy service
    
    // Aias for erm taxonomy service prototype alias
    Service.fn = Service.prototype;
    
    Service.fn.read = function (options) {
        var self = this;
        if (!empty(options.urlParams)) {
            $.extend(true, self.urlParams, options.urlParams);
        }
        $.get(self.endpoint, self.urlParams, options.callback);
    };
    
    return Service;
});