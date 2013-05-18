<?php

namespace Edm\Form;

use Edm\Model\User;

/**
 *
 * @author ElyDeLaCruz
 */
class UserLoginForm extends EdmForm  {

    public function __construct($name = 'user-login-form', array $options = null) {

        // Call parent constructor
        parent::__construct($name);

        $this->setObject(new User());
        
        // Set method
        $this->setAttribute('method', 'post');
        
        // Create fieldset
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');
        
        // Screen Name or Email
        $this->add(array(
            'name' => 'screenName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Screen Name',
            ),
            'attributes' => array(
                'autofocus' => true,
                'required' => true,
                'size' => '46%',
                'maxlength' => 32,
                'id' => 'screenName'
            )
        ));
        
        // Password
        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password'
            ),
            'attributes' => array(
                'required' => true,
                'size' => '46%',
                'maxlength' => 32,
                'id' => 'password'
            )
        ));

        // Submit and Reset Fieldset
        $this->add($submitAndReset);
    }

}
