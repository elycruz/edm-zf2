/**
 * Created by Ely on 5/24/2014.
 */
define(function () {
    return sjl.Extendable.extend(function AbstractModel(data) {
            var self = this;
            self.store = {};
            self.defaults = {};
            self.parse(data);
            self.init();
        },
        {
            parse: function (data) {
                if (!sjl.classOfIs(data, 'Object')) {
                    return;
                }

            },

            has: function (key) {
                return sjl.isset(this.get(key));
            },

            get: function (key, args, raw) {
                args = args || null;
                raw = raw || false;
                var retVal = null;
                if (typeof key === 'string' && $.isPlainObject(hash)) {
                    retVal = this._namespace(key, hash);
                    if (typeof retVal === 'function' && sjl.empty(raw)) {
                        retVal = args ? retVal.apply(this, args) : retVal.apply(this);
                    }
                }
                return retVal;
            },

            set: function (key, value) {
                sjl.namespace(key, this.store, value);
                return this;
            },

            delete: function (key) {
                delete sjl.namespace(key);
                return this;
            },

            /**
             * @method _callSetterForKey
             * @param key
             * @param value
             * @protected
             * @return {void}
             */
            _callSetterForKey: function (key, value) {
                var setterFunc = 'set' + sjl.camelCase(key, true),
                    self = this;
                if (sjl.isset(self[setterFunc])) {
                    self[setterFunc](value);
                }
                else {
                    self.set(key, value);
                }
            },

            init: function () { }
        });
});