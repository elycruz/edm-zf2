define(function () {
    
    function TermTaxonomyTrait (options) {
        
        var self = this;
        
        self.defaultOptions = {
            preloadTraits: false,
            termTaxService: null
        };
        
        $.extend(self, self.defaultOptions, options);
        
        if (self.preloadTraits) {
            self.getTermTaxonomyService();
        }
        
        delete self.defaultOptions;
    }
    
    var F = TermTaxonomyTrait;
    
    F.fn = F.prototype;
    
    F.fn.getTermTaxonomyService = function () {
        if (empty(this.termTaxService)) {
            this.termTaxService = app.serviceFactory.get('TermService');
        }
        return this.termTaxService;
    };
    
    return F;

});
