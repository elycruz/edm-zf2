<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @todo implement the errors info of the edm form element
 * @todo add red asterisk for required items
 */

namespace Edm\Form\Helper;

use Zend\View\Helper\AbstractHtmlElement,
        Zend\Form\Element;

/**
 * Wraps a form element within a div 
 * @todo finish up this view helper
 * @author ElyDeLaCruz
 */
class EdmFormElement extends AbstractHtmlElement {

    protected $defaultWrapperElmName = 'div';
    protected $defaultAttribs = array('class' => 'form-item');
    
    public function __construct() {}
    
    public function __invoke(Element $element) {
        $output = '';
        $view = $this->getView();
        
        // Opening div tag
        $wrapperElmName = $this->defaultWrapperElmName;
        $output .= '<' . $wrapperElmName;
        $output .= $this->htmlAttribs($this->defaultAttribs);
        $output .= '>';
        
        // Required
        $required = $element->getAttribute('required') ? 
            '<span class="red">*</span>' : '';
        
        // Label
        $label = $element->getLabel();
        if ($label) {
            $output .= $required . $view->formLabel($element) . '<br />' . PHP_EOL;
        }
        
        // Form element
        $output .= $view->formElement($element) . PHP_EOL;
        
        // Messages
        $messages = $element->getMessages();
        if ($messages) {
            $output .= '<div class="errors">';
            $output .= $view->htmlList($messages);
            $output .= '</div>';
        }
        
        $output .= '</'. $wrapperElmName .'>';
        return $output;
    }
    
}
