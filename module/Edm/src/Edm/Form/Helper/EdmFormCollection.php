<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Form\Helper;

use Zend\Form\View\Helper\FormCollection,
        Zend\Form\ElementInterface;

/**
 * Description of EdmFormCollection
 *
 * @author ElyDeLaCruz
 */
class EdmFormCollection extends FormCollection {
    public function __invoke(ElementInterface $element = null, $wrap = true) {
        $this->defaultElementHelper = 'edmformelement';
        return parent::__invoke($element, $wrap);
    }
}
