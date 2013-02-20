define(['knockout'], function(ko) 
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
         * @todo change self.module to self.entrypoint
         */ 
        self.module = null;

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

        // Extend this with options from outside
        if (!empty(options)) {
            return $.extend(this, options);
        }
    }

    // Alias View prototype
    View.fn = View.prototype;

    /**
     * Returns a list of views if any
     * @return array || null
     */
    View.fn.getViews = function () {
        var retVal = null;
        if (!empty(this.stackModel)) {
            retVal = this.stackModel.getViews();
        }
        else {
            retVal = this.views;
        }
        return retVal;
    };

    /**
     * Returns an empty object or ul attributes if specified
     * @return mixed object 
     */
    View.fn.getAttribs = function () {
        var retVal = {}, self = this;
        
        if (!empty(self.ulAttribs )) {
            retVal = self.ulAttribs;
        }
        else if (!empty(self.attribs)) {
            retVal = self.attribs;
        }
        
        return retVal;
    };

    // Return View
    return View;

});
    