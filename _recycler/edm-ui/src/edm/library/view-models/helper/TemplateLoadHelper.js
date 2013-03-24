define(['jquery'], function () {
    /**
     * Constructor  
     * @return default
     */
    function TemplateLoadHelper () {
        var self = this;
        self.removeOnReAttach = true;
    }
    var F = TemplateLoadHelper;
    
    F.fn = F.prototype;

    F.fn.checkTemplateIds = function (ids) {
        ids.filter(function (id) {
            return $('#' + id).length != 0;
        });
    };
    
    /**
     * @param config Object
     * @param owner Constructor | Function | Object
     * @param callbackContext Object optional
     * @param callback mixed [string, function];  
     *  I.e. if string, called on context callbackContext[callback] 
     *  skipped if no context
     * @return Object;  config with multiple flags set
     */
    F.fn.loadFromConfig = function (config, owner, callbackContext, callback) {
        
        var context = callbackContext || null;
        
        if (!empty(owner) && typeof owner !== 'string' 
            && owner.fn.tmplsLoaded === true) {
            return;
        }
       
        require([config.tmplsSrc], function (tmpls) {
            if (!empty(owner) && typeof owner !== 'string' 
                && owner.fn.tmplsLoaded === true) {
                return;
            }
            
            var notAttached = config.ids.filter(function (id) {
                return $('#' + id).length === 0;
            });
                        
            // @todo revisit this paradigm;  See if there is a more secure way
            // to do the following
            if (notAttached.length > 0) {
                if (this.removeOnReAttach) {
                    config.ids.each(function (id) {
                        $('#' + id).remove();
                    });
                }
                $('body').append(tmpls);
            }
            
            if (!empty(owner) && typeof owner !== 'string') {
                owner.fn.tmplsLoaded = true;
            }
            
            if (isset(context)) {
                if (isset(callback)) {
                    if (typeof callback === 'string') {
                        context[callback]();
                    }
                    else if (typeof callback === 'function') {
                        callback.apply(context);
                    }
                }
            }
            else if (callback && typeof callback === 'function') {
                callback();
            }
        });
            
        if (!empty(owner) && typeof owner !== 'string') {
            owner.fn.processed = true;
        }
            
        return config;
    };
    
    return F;

});