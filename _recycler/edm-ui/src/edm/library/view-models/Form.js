define([
    'library/ui/HtmlElement'
    ], function () {
        function Form (options) {
            
            var self = this
            
            ;
            
            self.options = {
            
            };
            
            $.extend(self, self.options, options);
        
        }
        
        Form.fn = Form.prototype;
        
        return Form;
    
    });

