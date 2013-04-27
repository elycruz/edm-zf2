<?php

namespace Edm\Form;

use Edm\Form\ContactFieldset,
    Edm\Form\UserFieldset;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class UserForm extends EdmForm  {

    public function __construct($name = 'term-taxonomy-form', array $options = null) {

        // Set service locator if injected manually
        if (isset($options) && isset($options['serviceLocator'])) {
            $this->setServiceLocator($options['serviceLocator']);
        }

        // Call parent constructor
        parent::__construct($name);

        // Set method
        $this->setAttribute('method', 'post');
        
        // Create fieldset
        $contactFieldset = new ContactFieldset('contact');
        $userFieldset = new UserFieldset('user');
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');

        // Set value options for status
        $userFieldset->get('status')->setValueOptions(
                $this->getTaxonomySelectElmOptions(array(
                    'taxonomy' => 'user-status',
                    'defaultOption' => array(
                        'value' => 'pending-activation',
                        'label' => '-- Select a User Status --'
                    )
                )));
        
        // Set value options for status
        $userFieldset->get('accessGroup')->setValueOptions(
                $this->getTaxonomySelectElmOptions(array(
                    'taxonomy' => 'user-group',
                    'defaultOption' => array(
                        'value' => 'cms-manager',
                        'label' => '-- Select an Access Group --'
                    )
                )));
        
        // Set value options for status
        $userFieldset->get('role')->setValueOptions(
                $this->getTaxonomySelectElmOptions(array(
                    'taxonomy' => 'user-group',
                    'defaultOption' => array(
                        'value' => 'user',
                        'label' => '-- Select a User Group --'
                    )
                )));

        // Set value options for parent_id
        // Term Feildset
        $this->add($contactFieldset);

        // Term Taxonomy Fieldset
        $this->add($userFieldset);

        // Submit and Reset Fieldset
        $this->add($submitAndReset);
    }

}
