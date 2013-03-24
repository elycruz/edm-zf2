define(function() 
{
    /**
     * View constructor.  Encapsulates view functionality.
     * @return default
     */
    function View (options) 
    {
        var self = this;

        /**
         * Title partial;  For use in bread crumbs and page title
         * @var string default 'Page base title here'
         */
        self.title = 'Page base title here';

        /**
         * Href for link associated with this view
         * @var string default null
         */
        self.route = null;

        /**
         * Entry point for this view
         * @var Constructor default null
         */ 
        self.controller = null;

        /**
        * Html source path
        * @var string
        */
        self.htmlSrc = '';

        /**
        * Templates source path
        * @var string
        */
        self.tmplsSrc = '';

        /**
        * Html string
        * @var string
        */
        self.html = '';

        /**
        * Templates string;  I.e., contents of html file containing 
        *  script tag templates
        * @var string
        */
        self.tmpls = '';

        /**
        * Templates attached flag
        * @var boolean default false
        */
        self.tmplsAttached = false;

        /**
        * Templates loaded flag
        * @var boolean default false
        */
        self.tmplsLoaded = false;

        /**
        * Html loaded flag
        * @var boolean default false
        */
        self.htmlLoaded = false;

        /**
         * Array of sub views if any
         * @var array default null
         */
        self.views = null;

        if (!empty(options)) {
            if (!empty(options.attribs)) {
                delete options.attribs;
            }
            $.extend(this, options);
        }
        
    }

    // Alias View prototype
    View.fn = View.prototype;

    /**
     * Returns a list of items (views)  if any
     * @return array || null
     */
    View.fn.getItems = function () {
        return this.collection;
    };
    
    View.fn.getUlAttribs = function () {
        var retVal = {};
        if (this.ulAttribs) {
            retVal = this.ulAttribs;
        }
        return retVal;
    };

    // Return View
    return View;

});
    