<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\View\Helper;

use Zend\View\Helper\AbstractHtmlElement,
        Zend\Form\Element;

/**
 * Wraps a form element within a div 
 * @todo finish up this view helper
 * @author ElyDeLaCruz
 */
class EdmOutputFlashMessages extends AbstractHtmlElement {

    public function __construct() {}
    
    public function __invoke() {
        $view = $this->getView(); 
        $output = '';
        if (isset($view->messages) && count($view->messages) > 0) {
            $output = '<ul>';
            foreach ($this->getView()->messages as $msg) {
                $output .= '<li>' . $msg . '</li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }
    
}
