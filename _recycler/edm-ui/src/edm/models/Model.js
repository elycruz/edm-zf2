define(function () {
    
    function Model (options) {
        
        var self = this;
        
        self.options = {
            uiid: null,
            source: null
        };
        
        $.extend(self, self.options, options);
        
        delete self.options;
    }
    
    Model.fn = Model.prototype;
    
    Model.fn.create = function (data) {
        trace(data);
    };
    
    Model.fn.update = function (data) {
        trace(data);
    };
    
    Model.fn.read = function (data) {
        trace(data);
    };
    
    Model.fn.remove = function (data) {
        trace(data);
    };
    
    Model.fn.get = function (key) {
        return this.source[key];
    };
    
    Model.fn.set = function (key, value) {
        this.source[key] = value;  
    };
    
    return Model;

});

