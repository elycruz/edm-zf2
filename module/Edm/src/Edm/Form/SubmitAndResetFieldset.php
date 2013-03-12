<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset;

/**
 * Description of SubmitAndResetFieldset
 *
 * @author ElyDeLaCruz
 */
class SubmitAndResetFieldset extends Fieldset {

    public function __construct($name = null, $options = array()) {
        $this->add(array(
            'name' => 'reset',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'Reset',
                'class' => 'btn big-btn'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn big-btn'
            )
        ));
        parent::__construct($name, $options);
    }

}
