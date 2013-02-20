
<?php
/**
 * @author ElyDeLaCruz
 */
namespace Edm\Controller\Action;
class AbstractHelper
    extends Zend\Mvc\Controller\Action_Helper_Abstract
{
    /**
     * Our Zend Layout variable which makes our layout easily accessible from
     * action helpers.
     * @var Zend_Layout
     */
    protected $_layout;
    
    /**
     * Sets the layout variable for this controller action helper to the mvc
     * instance.
     * @param Zend_Layout $layout
     */
    protected function _getLayout(Zend_Layout $layout = null)
    {
        if (!empty($layout)) {
            $this->_layout = $layout;
        }
        else {
            $this->_layout = Zend_Layout::getMvcInstance();
        }
        return $this->_layout;
    }
}
