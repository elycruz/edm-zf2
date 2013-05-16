<?php

namespace Edm\Form\Element;

use Zend\Form\Element;

/**
 * Description of KeyValuePairElement
 *
 * @author ElyDeLaCruz
 */
class KeyValuePairElement extends Element {
    //put your code here
    
    public $key;
    
    public $value;
    
    public $rsltValue;
    
    protected $keyField;
    
    protected $valueField;
    
    protected $keyFieldConfig;
    
    protected $valueFieldConfig;
    
    public function init() {
        
    }
    
}

