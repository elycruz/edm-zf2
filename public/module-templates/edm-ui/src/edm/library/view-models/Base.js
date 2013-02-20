define(['jquery'],
function () {
    
    function Base (options) {
        $.extend(this, options);
    }
    
    Base.fn = Base.prototype;
    
    Base.fn.setOptions = function (options, deep) {
        deep = deep || false;
        $.extend(deep, this, options);
    };

    return Base;
});

