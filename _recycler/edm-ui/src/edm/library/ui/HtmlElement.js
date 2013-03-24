define(['knockout', 'library/view-models/Base'], function (ko, Base) 
{
    function HtmlElement (options) {
        var self = this;
        $.extend(true, self, options);
        if (typeof self.attribs !== 'function') {
            self.attribs = ko.observable(self.attribs);
        }
        Base.apply(self);
    }
    
    HtmlElement.fn = 
        HtmlElement.prototype =
            Object.create(Base.fn);

    HtmlElement.fn.getText = function () {
        return this.text;
    };
    
    HtmlElement.fn.setText = function (text) {
        this.text = text;
    };

    HtmlElement.fn.getAttribs = function () {
        return this.attribs();
    };
    
    HtmlElement.fn.getAttrib = function (key) {
        return this.attribs().key;
    };
    
    HtmlElement.fn.setAttribs = function (attribs) {
        this.attribs(attribs);
    };
    
    HtmlElement.fn.setAttrib = function (key, value) {
        this.attribs().key = value;
    };
    
    return HtmlElement;
    
});