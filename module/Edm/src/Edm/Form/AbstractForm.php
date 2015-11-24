<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 12:25 PM
 */

namespace Edm\Form;

use Zend\Form\Form,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

class AbstractForm extends Form
    implements  ServiceLocatorAwareInterface {

    use ServiceLocatorAwareTrait;

}
