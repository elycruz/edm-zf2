/**
 * File name and purpose here
 * http://elycruz.com/
 *
 * Copyright 2010, Ely De La Cruz
 * Released under the MIT and GPL Version 2 licenses.
 * http://elycruz.com/code-license
 *
 * Date: Sat Feb 13 22:33:48 2010 -0500
 * 
 * This class deals with the edm back end.  If you would like to use
 * this class for the front end extend it as it may break the backend 
 * functionality if altered for the front end.
 */
var edm = 
{
    /**
     * Flag for tracking whether or not edm is initialized
     * @var boolean default false
     */
    initialized: false,
    
    /**
     * Has to be manually set so that edm knows what to do internally
     * @var boolean default false
     */
    useModule: false,

    /**
     * Has to be manually set so that edm knows what to do internally
     * @var boolean default true
     */
    useController: true,

    /**
     * Has to be manually set so that edm knows what to do internally
     * @var boolean default true
     */
    useAction: true,

    /**
     * Positions where route defined values are located; 
     * I.e. values that do not have a visible key
     * This allows us to make the keys visible if we so desire
     * the values should be objects with name, index, 
     * and regex describing its content
     * @var array empty
     */ 
    invisibleRouteKeys: [],
    
    /**
     * Location functions
     * @var object
     */
    location: 
    {        
        
        pathnameToLiteral: function()
        {
            var i = 0, output = {}, 
            key = null, value = null, j, s;
            
            // Get pathname to operate on
            s = arguments[0] || window.location.pathname;

            // Pathname array
            s = s.split('/');
            
            // Remove first empty item from array
            s.shift();

            // Check if we need to pop out our 
            // module/controller/action variables
            try {
                if (edm.useModule === true) {
                    edm.response.module = s[i] || null;
                    edm.request.module = s[i] || null;
                    delete s[i];
                    // offset from where to start iterating from
                    i += 1;
                }
                if (edm.useController === true) {
                    edm.response.controller = s[i] || 'index';
                    edm.request.controller = s[i] || 'index';
                    // offset from where to start iterating from
                    delete s[i];
                    i += 1;
                }
                if (edm.useAction === true) {
                    edm.response.action = s[i] || 'index';
                    edm.request.action = s[i] || 'index';
                    delete s[i];
                    i += 1;
                }
                if (edm.invisibleRouteKeys.length > 0) {
                    for (j = 0; j < edm.invisibleRouteKeys.length; j += 1) {
                                 
                        var ik = edm.invisibleRouteKeys[j];
                        
                        if (!ik.regex.test(s[i])) {
                            continue;
                        }
                        
                        if (!edm.response.routeDefinedParams[j]){
                            edm.response.routeDefinedParams.push(s[i]);
                        }
                        
                        if (ik.render) {
                            edm.request.routeDefinedParams.push(s[i]);
                        }
                        else if (ik.makeParam) {
                            edm.request.params[ik.name] = s[i];
                        }
                        
                        ik.value = s[i];
                        delete s[i];
                        i += 1;
                    }
                }
            }
            catch(e) {
                alert(e);
            }

            // If we have more than '/module/controller/view' declared put
            // them into our object
            if( s.length > 0) {
                for (key in s) {
                    value = s[i+1];
                    if (key && value) {
                        output[s[i]] = decodeURI(value);
                    }
                    i += 2;
                }
            }
            
            return output;
        }, // end location pathname
        
        /**
         * Returns the location.search as an object or array literal
         * @param toArray Boolean 
         * @return Object || Array depending on `toArray` value
         */
        searchStringToLiteral:function(toArray)
        {
            var s = window.location.search, i = null,
            output = null, item = null, key = null, value = null;
            if(s){
                output = toArray ? [] : {};
                s = s.slice(1, s.length);
                if( s.indexOf('&') && s.lastIndexOf('&') != (s.length - 1) ){
                    s = s.split('&');
                    for( i = 0; i < s.length; i += 1 ){
                        item = s[i].split('=');
                        key = item[0];
                        value = item[1];
                        output[key] = value;
                    }
                }
                else {
                    output[s.split('=')[0]] = s.split('=')[1];
                }
            }
            else {
                output = false;
            }
            return output;
            
        }, // end location.search
        
        /**
         * Returns the location (search | pathname) literal to string
         * @param objOrArray Object || Array is the literal
         * @param classic Boolean tells whether to return 'x=1&y=2' ||
         * '/x/1/y/2/' format, default is '/x/1/y/2/'
         * @return String
         */
        literalToString: function(objOrArray, classic)
        {
            var obj, terms = [],
            search = classic ? '?' : '/',
            sep1 = classic ? '=' : '/',
            sep2 = classic ? '&' : '/',
            key;
            
            // Join invisible route keys first if any and
            // if we are using the more modern route scheme
            if (edm.request.routeDefinedParams.length > 0
                && search == '/') {
                search += edm.request.routeDefinedParams.join(sep1);
                search += '/';
            }

            if(typeof objOrArray === 'object' ||
                typeof objOrArray === 'array') {
                obj = objOrArray;
                for(key in obj){
                    terms.push(key + sep1 + obj[key]);
                }
                search += terms.join(sep2);
                return search;
            }
            else {
                return null;
            }
            
        } // end literalToString
        
    }, // end location
    
    // Will inherit from the location.response object
    request: 
    {
        module: null,
        controller: null,
        action: null,
        uri: null,
        id: null,
        params: {},
        routeDefinedParams: [],
        classicParamsBln: false,
        
        getModuleName: function () {
            return edm.request.module;
        },
        getActionName: function () {
            return edm.request.action;
        },
        getControllerName: function () {
            return edm.request.controller;
        },
        setModuleName: function (value) {
            edm.request.module = value;
            return edm.request;
        },
        setControllerName: function (value) {
            edm.request.controller = value;
            return edm.request;
        },
        setActionName: function (value) {
            edm.request.action = value;
            return edm.request;
        },
        
        clearParams: function() {
            edm.request.params = {};
            edm.request.routeDefinedParams = [];
            return edm.request;
        },
        getParam: function (name) {
            return edm.request.params[name];  
        },
        setParam: function (name, value) {
            edm.request.params[name] = value;
            return edm.request;
        },
        
        setRouteDefinedParam: function (value) {
            edm.request.routeDefinedParams.push(value);
            return edm.request;
        },
        
        toString: function ()
        {
            var url = '';
            
            if (edm.request.module !== null) {
                url += '/' + edm.request.module;
            }
            if (edm.request.controller !== null) {
                url += '/' + edm.request.controller;
            }
            if (edm.request.action !== null) {
                url += '/' + edm.request.action;
            }
            if (edm.request.id) {
                url += '/' + edm.request.id;
            }
            
            // Append any params that are needed for request
            edm.request.url = 
            url += edm.location.literalToString(edm.request.params, false);

            return edm.request.url;
        },
        
        make: function () 
        {
            window.location.href = edm.request.toString();
        }
        
    },
    
    response: {
        paramFlags: {
            PATHNAME_PARAMS: 1,
            SEARCH_PARAMS: 2,
            SEARCH_AND_PATHNAME_PARAMS: 3
        },
        id: null,
        module: null,
        controller: null,
        action: null,
        params: {},
        routeDefinedParams: [],
        classicParamsBln: false,
        getParams: function(paramFlag) 
        {
            switch(paramFlag) {
                case 1:
                    edm.response.params = edm.location.pathnameToLiteral();
                    break;
                case 2:
                    edm.response.params = 
                    edm.location.searchStringToLiteral(false);
                    break;
                case 3:
                    edm.response.params = edm.location.pathnameToLiteral();
                    var searchParams = 
                    edm.location.searchStringToLiteral(false);
                    // @todo fix search_and_pathname case in edm-1.0.0.js
                    edm.response.params = 
                    $.edom.extendObject(searchParams, edm.response.params);
                    break;
                default:
                    edm.response.params = edm.location.pathnameToLiteral();
                    break;
            }
            return edm.response.params;
        },
        getModuleName: function() {
            return edm.response.module;
        },
        getControllerName: function() {
            return edm.response.controller;
        },
        getActionName: function() {
            return edm.response.action;
        }
    },
    
    init: function() {
        if (!edm.initialized) {
            edm.response.params = edm.response.getParams(
                edm.response.paramFlags.PATHNAME_PARAMS);
            edm.request.params = edm.response.params;
            edm.initialized = true;
        }
    }
}; // end edm