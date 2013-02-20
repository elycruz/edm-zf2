define([
    'library/data/Iterator',
    'es5',
    'jquery'
    ], function (Iterator)  {
        'use strict';
    
        function Collection(options) 
        {
            var self = this, emptyOptions;
        
            // Inherit instance properties from iterator
            Iterator.apply(self, [options]);
            
            /**
            * Item Constructor
            * @var mixed string | constructor | other
            */
            self.itemConstructor = !emptyOptions && 
                !empty(options.itemConstructor) ? 
                    options.itemConstructor : null;
        
            emptyOptions = empty(options);
            
            if (!emptyOptions && !empty(options.collection)) {
                self.collection = [];
                self.addItems(options.collection);
            }
            
            // Extend self with options
            if (!emptyOptions) {
                $.extend(self, options);
            }
        }
    
        Collection.fn = 
                Collection.prototype = 
                    new Iterator();
    
        Collection.fn.addItem = function (item) 
        {
            var self = this;
        
            if (self.itemConstructor !== null && item
                instanceof self.itemConstructor === false) {
                self.collection.push(new self.itemConstructor(item));
            }
            else if (!empty(item)) {
                self.collection.push(item);
            }
        };

        Collection.fn.addItems = function (collection) 
        {
            var self = this, item;
            if (!Array.isArray(collection)) {
                throw new Error('Collection.addItems expects' +
                    ' parameter 1 to be an Array. ' + 
                    ' parameter received is of type ' + (typeof collection));
                return;
            }
            collection.forEach(function (item) {
                self.addItem(item);
            });
        };

        Collection.fn.length = function () {
            return this.collection.length;
        };
        
        Collection.fn.data = function (data) {
            if (!empty(data) && Array.isArray(data)) {
                if (typeof this.itemConstructor === 'function') {
                    this.collection = [];
                    this.addItems(data);
                }
                else {
                    this.collection = data;
                }
            }
            else {
                return this.collection;
            }
            return this;
        };
        
        Collection.fn.getItems = function () {
            return this.collection;
        };
    
        Collection.fn.removeItemsBy = function (key, value) {}
        
        /**
         * Filters array and returns collection with a key of value
         * @param key mixed [(string|namespaced string), object]
         * @param value mixed required for @param key of string
         * @param collection array optional;  Collection to search in
         * @return array
         */
        Collection.fn.findItemsBy = function (key, value, collection) {
        
            var keyParts, parent, i;
        
            // If hierarchical string
            if (key.indexOf('.') !== -1) {
                keyParts = key.split('.'); 
            }
                
            collection = collection || this.collection;
            return collection.filter(function (data) { 
                var retVal = false;
                if (!empty(keyParts) && keyParts.length > 0) {
                    parent = data;
                    for (i in keyParts) {
                        i = keyParts[i];
                        if (parent.hasOwnProperty(i)) {
                            retVal = parent[i] == value;
                            if (retVal === true) {
                                return retVal;
                            }
                            parent = parent[i]
                        }
                    } // for
                } // if 
                
                // If key is object
                else if (typeof key === 'object') {
                    var keyKeys     = Object.keys(key),
                    validKeys   = keyKeys.filter(function (x){
                        return data[x] == key[x];
                    });
                    
                    retVal = validKeys.length == keyKeys.length;
                }
                
                // if normal string key
                else if (data.hasOwnProperty(key)) {
                    retVal = data[key] == value;
                }
            
                return retVal;
            });
        };
        
        // Inherit prototype functions from Iterator
        $.extend(Collection.fn, Iterator.fn);
    
        return Collection;
    
    }); // define