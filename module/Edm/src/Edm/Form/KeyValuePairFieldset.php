<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset;

/**
 * Description of KeyValuePairFieldset
 * @author ElyDeLaCruz
 */
class KeyValuePairFieldset extends Fieldset {

    public $keySize = 28;
    
    public $valueSize = 28;
    
    public function __construct($name = 'key-value-pair', $options = array()) {
        parent::__construct($name, $options);

        // Key
        $this->add(array(
            'name' => 'key',
            'type' => 'text',
            'options' => array(
                'label' => 'Key',
            ),
            'attributes' => array(
                'required' => false,
                'size' => 28,
                'id' => 'key'
            )
        ));
        
        // Value
        $this->add(array(
            'name' => 'value',
            'type' => 'text',
            'options' => array(
                'label' => 'Value',
            ),
            'attributes' => array(
                'required' => false,
                'size' => 28,
                'id' => 'value'
            )
        ));
        
    }

}
