define(function () {
    
    function PostTrait (options) {
        
        var self = this
        
        ;
        
        self.defaultOptions = {
            postService: null,
            commentService: null,
            termService: null,
            termTaxService: null
        };
        
        $.extend(self, self.defaultOptions, options);
    
    }
    
    var F = PostTrait;
    
    F.fn = F.prototype;
    
    return F;

});

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


