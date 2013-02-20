define(['service/Service'], function(Service) {
    var termService = new Service({
        url: '/term',
        urlParams: {
            format: 'json'
        }
    });
    
    termService.read({
        urlParams: null,
        callback: function (data) {
            trace(data);
        }
    });
    
    
    return termService;
});