<?php

/**
 * Description of HtmlMail
 *
 * @author ElyDeLaCruz
 */
class Edm_Mail_HtmlMail extends Zend_Mail {

    /**
     * The Script Path used for our view object which resides only within
     * this current instance of Edm_HtmlMail.
     * @var string
     */
    private static $_scriptPath;

    /**
     * Default view
     * @var Zend_View
     */
    static $_defaultView;

    /**
     * Local copy of Default view
     * @var Zend_View
     */
    protected $_view;

    /**
     * Constructs our HtmlMail object and calls the parents constructor
     * @param string $charset
     */
    public function __construct($charset = 'utf-8') {
        parent::__construct($charset);
        $this->_view = self::_getDefaultView();
    }

    /**
     * Sends an HTML template (view script)
     * @param string                        $template templates script path
     * @param Zend_Mail_Transport_Abstract  $transport
     * @param string                        $encoding
     * @return Edm_HtmlMail
     */
    public function sendHtmlTemplate($template, $transport = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
        $html = $this->_view->render($template);
        $this->setBodyHtml($html, $this->getCharset(), $encoding);
        return $this->send();
    }

    /**
     * Sets a view parameter for the html template to be sent.
     * @param string $property
     * @param mixed $value
     * @return Edm_HtmlMail 
     */
    public function setViewParam($property, $value) {
        $this->_view->__set($property, $value);
        return $this;
    }

    /**
     * Set multiple view params at once.
     * @param array $params
     * @return Edm_HtmlMail 
     */
    public function setViewParams(array $params) {
        foreach ($params as $key => $val) {
            $this->setViewParam($key, $val);
        }
        return $this;
    }

    /**
     * Set the view script path for the html template to be sent.
     * @param string $path
     */
    public static function setScriptPath($path) {
        self::$_scriptPath = $path;
        self::$_defaultView->setScriptPath(self::$_scriptPath);
    }

    /**
     * Returns the view object to be used for rendering our html template to be
     * sent.
     * @return Zend_View_Abstract
     */
    protected static function _getDefaultView() {
        if (self::$_defaultView === null) {
            self::$_defaultView = new Zend_View();
            if (empty(self::$_scriptPath)) {
                self::$_scriptPath = APPLICATION_PATH . '/views/scripts';
            }
            self::setScriptPath(self::$_scriptPath);
        }
        return self::$_defaultView;
    }

}