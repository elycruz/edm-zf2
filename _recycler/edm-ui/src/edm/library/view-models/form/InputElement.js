define([
    'library/ui/HtmlElement'
    ], function () {

        function InputElement (options) {
            var self = this;

            /**
            * Break tag with "cb" (clear: both) class after label
            * @var boolean
            */
            self.clearBreakAfterLabel = null;

            /**
            * Break tag after label
            * @var boolean
            */
            self.breakAfterLabel = null;

            /**
            * Template to attach to
            * @var mixed string | object
            */
            self.template = null;

            HtmlElement.apply(this, [options]);
        }

        // Solve inheritance issue
        InputElement.fn = 
            InputElement.prototype = 
                new HtmlElement();
        
        return InputElement;
    
    });