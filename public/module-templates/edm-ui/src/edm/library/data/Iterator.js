define(function () {
    
    'use strict';
    
    function Iterator (options) {
        
        var self = this;
        
        self.options = {
            pointer: 0,
            collection: []
        };
        
        $.extend(self, self.options, options);
        delete self.options;
    }
    
    Iterator.fn = Iterator.prototype;

    Iterator.fn.valid = function () {
        return (self.hasArray() && self.hasIndex());
    };
    
    Iterator.fn.next = function () {
        this.pointer += 1;
    };
    
    Iterator.fn.previous = function () {
        this.pointer -= 1;
    };
    
    Iterator.fn.hasIndex = function (index) {
        index = index || self.pointer;
        return self.hasArray() && isset(self.collection[index]);
    };
    
    Iterator.fn.hasArray = function () {
        return Array.isArray(self.collection);
    };
    
    Iterator.fn.rewind = function () {
        this.pointer = 0;
    };
    
    Iterator.fn.throwNotArrayException = function () {
        throw {
            message: 'Expected value of type Array instead saw ' + 
            (typeof self.collection),
            error: 'TypeException'
        };
    };

    return Iterator;

});

