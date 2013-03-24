define(function () {
    
    function PostModel (options) {
        
        var self = this;
        
        self.options = {
            // Map of post table columns
            source: {
                post_id     : null,
                parent_id   : null,
                title   : null,
                alias   : null,
                content : null,
                excerpt : null,
                hits    : null,
                listOrder       : null,
                commentStatus   : null,
                commentCount    : null,
                createdDate   : null,
                createdById   : null,
                lastUpdated   : null,
                lastUpdatedBy : null,
                checkedInDate : null,
                checkedOutDate: null,
                checkedOutById: null,
                userParams      : null,
            
                // post term relationships
                term_taxonomy_id: null,
                accessGroup     : null,
                status  : null,
                type    : null
            }
        };
        
        $.extend(true, self, self.options, options);
        
        delete self.options;
    }
    
    var F = PostModel;
    
    F.fn = F.prototype;
    
    F.fn.create = function (data) {
    };
    
    F.fn.update = function (data) {
    };
    
    F.fn.read = function (data) {
    };
    
    F.fn.remove = function (data) {
    };
    
    return F;

});

