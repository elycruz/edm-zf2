<?php
/**
 * @see http://www.rmauger.co.uk/2010/01/keeping-your-html-valid-with-zend-framework-tidy-and-firebug/
 */

class Edm_Filter_TidyFilter implements Zend_Filter_Interface {

    /**
     * @var tidy
     */
    protected $_tidy;
    
    /**
     * @var tidy
     */
    protected $_encoding = 'UTF8';
    
    /**
     * @var array
     */
    protected $_config = array('indent' => false,
        'output-xhtml' => true,
        'wrap' => false,
        'show-body-only' => true);

    /**
     * Filter the content with Tidy.
     *
     * @return string
     */
    public function filter($content) {
        $tidy = null;
        if (!empty($content)) {
            $tidy = $this->getTidy($content);
            $tidy->cleanRepair();
        }
        return (string) $tidy;
    }

    /**
     * Gets the Tidy object
     */
    public function getTidy($string) {
        if (!is_string($string)) {
            throw new InvalidArguementException('Expected string, got: ' .
                    get_type($string));
        }

        if (null === $this->_tidy) {
            $this->_tidy = new tidy();
        }

        $this->_tidy->parseString($string, $this->_config, $this->_encoding);
        return $this->_tidy;
    }

}