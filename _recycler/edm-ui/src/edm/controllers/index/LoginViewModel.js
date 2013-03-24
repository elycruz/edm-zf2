define(['libraryViewModule'], 
function (ViewModule) {
    
    function LoginViewModel (options) {
        
        var self = this;
        
        self.options = {
            title   : 'Login',
            content : '<!-- ko template: {name: "login-form-tmpl", ' 
                        + 'viewModel: loginForm} --><!-- /ko -->'
        };
        
        $.extend(self.options, options);
        
        ViewModule.apply(this, [self.options]);
    }
    
    LoginViewModel.fn = LoginViewModel.prototype =
        new ViewModule();
    
    return LoginViewModel;

});

