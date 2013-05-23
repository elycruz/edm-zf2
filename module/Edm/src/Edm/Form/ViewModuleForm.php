<?php

namespace Edm\Form;

use Edm\Form\UserParamsFieldset,
    Edm\Form\MixedTermRelFieldset,
    Edm\Form\ViewModuleFieldset;

/**
 * Description of ViewModuleForm
 * @author ElyDeLaCruz
 */
class ViewModuleForm extends EdmForm {

    public function __construct($name = 'view-module-form', array $options = null) {

        // Set service locator if injected manually
        if (isset($options) && isset($options['serviceLocator'])) {
            $this->setServiceLocator($options['serviceLocator']);
        }

        // Call parent constructor
        parent::__construct($name);

        // Set method
        $this->setAttribute('method', 'post');
        
        // View Module Fieldset
        $viewModuleFieldset = new ViewModuleFieldset('view-module-fieldset');
        
        // Mixed Term Rel Fieldset
        $mixedTermRelFieldset = new MixedTermRelFieldset('mixed-term-rel-fieldset');
        
        // User Params Fieldset
        $userParamsFieldset = new UserParamsFieldset('user-params-fieldset');
        
        // Add Status values
        $status = $mixedTermRelFieldset->get('status');
        $status->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-status',
                'defaultOption' => array(
                    'value' => 'draft',
                    'label' => '-- Select a Status --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        // Default the form elements value
        $status->setValue('published');
        
        // Add Access Group values
        $accessGroup = $mixedTermRelFieldset->get('accessGroup');
        $accessGroup->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'user-group',
                'defaultOption' => array(
                    'value' => 'user',
                    'label' => '-- Select an Access Group --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Default the access group to guest
        $accessGroup->setValue('guest');
        
        // Add Type values
        $type = $viewModuleFieldset->get('type');
        $type->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'view-module-type',
                'defaultOption' => array(
                    'value' => 'post',
                    'label' => '-- Select a View Module Type --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Default the type to "html" type
        $type->setValue('html');
        
        // Add Position values
        $position = $mixedTermRelFieldset->get('term_taxonomy_id');
        $position->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'position',
                'defaultOption' => array(
                    'value' => null,
                    'label' => '-- Select a Position --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_taxonomy_id',
                    'label' => 'term_name'
                )
        )));
        
        // Add View Module Fieldset
        $this->add($viewModuleFieldset);
        
        // Add Mixed Term Rel Fieldset
        $this->add($mixedTermRelFieldset);
        
        // Add User Params Fieldset
        $this->add($userParamsFieldset);
        
        // Submit and Reset Fieldset
        $this->add(new SubmitAndResetFieldset('submit-and-reset'));
    }

}
