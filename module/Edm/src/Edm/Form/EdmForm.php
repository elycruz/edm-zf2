<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Form,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Form\TermTaxonomyOptionsTrait;

/**
 * Description of EdmForm
 *
 * @author ElyDeLaCruz
 */
class EdmForm extends Form  
    implements  ServiceLocatorAwareInterface, 
                TermTaxonomyServiceAware
{
    use ServiceLocatorAwareTrait, 
        TermTaxonomyServiceAwareTrait,
        TermTaxonomyOptionsTrait;
}
