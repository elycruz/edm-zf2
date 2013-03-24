define(function () {
    
    function Post (options) {
        
        var self = this
        
        ;
        
        self.defaultOptions = {
            postService: null,
//            postModel: new PostModel(),
            commentsModel: null,
            termsModel: null,
            termTaxModel: null
        };
        
        $.extend(self, self.defaultOptions, options);
    
    }
    
    var F = Post;
    
    F.fn = F.prototype;
    
    F.fn.getPostService = function () {
        if (empty(this.postService)) {
            this.postService = app.serviceFactory.get('PostService');
        }
        return this.postService;
    };
    
    F.fn
    
    return F;

});

