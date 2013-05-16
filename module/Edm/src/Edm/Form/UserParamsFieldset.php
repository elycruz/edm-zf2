<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset;

/**
 * Description of TermFieldset
 *
 * @author ElyDeLaCruz
 */
class UserParamsFieldset extends Fieldset {

    public function __construct($name = 'user-params', $options = array()) {

        parent::__construct($name, $options);

        $this->attributes['class'] = 'user-params';
        
        // User Params
        $this->add(array(
            'name' => 'userParams',
            'type' => 'Zend\Form\Element\Collection',
            'options' => array(
                'count' => 2,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'Edm\Form\KeyValuePairFieldset',
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
