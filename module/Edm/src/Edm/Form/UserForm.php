<?php

declare(strict_types=1);

namespace Edm\Form;

use Edm\Form\Fieldset\ContactFieldset,
    Edm\Form\Fieldset\UserFieldset,
    Edm\Form\Fieldset\SubmitAndResetFieldset;

/**
 * Description of UserForm
 *
 * @author Ely
 */
class UserForm extends TermTaxonomyServiceAwareForm {
   
    use TermTaxonomyOptionsTrait;
    
    public function __construct(string $name = 'user-form', array $options = null) {
        // Call parent constructor
        parent::__construct($name, $options);
        
        // Set method
        $this->setAttribute('method', 'post');
        
        // Create fieldset
        $contactFieldset = new ContactFieldset('contact');
        $userFieldset = new UserFieldset('user');
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');
        
        // Set value options for parent_id
        // Term Feildset
        $this->add($contactFieldset);

        // Term Taxonomy Fieldset
        $this->add($userFieldset);

        // Submit and Reset Fieldset
        $this->add($submitAndReset);
    }
    
    public function init () {
        // Get user fieldset
        $userFieldset = $this->get('user');

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
    }

}