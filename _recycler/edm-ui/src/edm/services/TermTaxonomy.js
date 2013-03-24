define(['service/Service'], function(Service) {
    var termTaxService = new Service({
        url: '/term-taxonomy',
        urlParams: {
            format: 'json'
        }
    });
    
    termTaxService.read({
        callback: function (data) {
            trace(data);
        }
    });
    
});