<?php

namespace Edm\Form\Fieldset;

use Zend\Form\Fieldset;

/**
 * Description of SubmitAndResetFieldset
 *
 * @author ElyDeLaCruz
 */
class SubmitAndResetFieldset extends Fieldset {

    public function __construct($name = 'submit-and-reset', $options = array()) {

        // Call parent constructor
        parent::__construct($name, $options);

        // Reset btn
        $this->add(array(
            'name' => 'reset',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'Reset',
                'class' => 'btn big-btn'
            )
        ));

        // Submit btn
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn big-btn'
            )
        ));
    }

}
