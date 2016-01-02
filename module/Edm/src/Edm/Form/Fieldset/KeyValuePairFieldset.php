<?php

namespace Edm\Form\Fieldset;

/**
 * Description of KeyValuePairFieldset
 *
 * @author Ely
 */
class KeyValuePairFieldset extends Fieldset {

    public $keySize = 34;
    
    public $valueSize = 34;
    
    public function __construct($name = 'key-value-pair', $options = array()) {
        // Call parent constructor
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
                'size' => $this->keySize,
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
                'size' => $this->valueSize,
                'id' => 'value'
            )
        ));
    }

}