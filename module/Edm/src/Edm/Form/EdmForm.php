<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Form,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface;
/**
 * Description of EdmForm
 *
 * @author ElyDeLaCruz
 */
class EdmForm extends Form  implements ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;
}
