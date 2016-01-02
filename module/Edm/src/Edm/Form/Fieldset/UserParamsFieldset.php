<?php

namespace Edm\Form\Fieldset;

use Zend\Form\Fieldset;

/**
 * Description of UserParamsFieldset
 *
 * @author Ely
 */
class UserParamsFieldset extends Fieldset {

    public function __construct($name = 'user-params', $options = array()) {

        parent::__construct($name, $options);

        $this->attributes['class'] = $name;
        
        // User Params
        $this->add(array(
            'name' => 'userParams',
            'type' => 'Zend\Form\Element\Collection',
            'options' => array(
                'count' => 3,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'KeyValuePairFieldset',
                    'options' => array(
                        'label' => 'Key value pair'
                    )),
            ),
//            'attributes' => array(
//                'required' => false,
//                'id' => 'userParams'
//            ),
        ));
    }

}