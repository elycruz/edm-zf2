<?php

namespace Edm\Form;

use Edm\Form\MenuPageRelFieldset,
    Edm\Form\MixedTermRelFieldset,
    Edm\Form\PageFieldset,
    Edm\Form\UserParamsFieldset;

/**
 * Description of PageForm
 * @author ElyDeLaCruz
 * @todo think about having an some sort of an options table for the appliaction; e.g., _application_options.
 */
class PageForm extends EdmForm {

    public function __construct($name = 'page-form', array $options = null) {

        // Set service locator if injected manually
        if (isset($options) && isset($options['serviceLocator'])) {
            $this->setServiceLocator($options['serviceLocator']);
        }

        // Call parent constructor
        parent::__construct($name);

        // Set method
        $this->setAttribute('method', 'post');

        // Page Fieldset
        $pageFieldset = new PageFieldset('page-fieldset');
        
        // Mixed Term Rel Fieldset
        $mixedTermRelFieldset = new MixedTermRelFieldset('mixed-term-rel-fieldset');
        
        // Menu Page Rel Fieldset
        $menuPageRelFieldset = new MenuPageRelFieldset('menu-page-rel-fieldset');
        
        // Mvc Params field collection
        $mvc_params = new UserParamsFieldset('mvc-params-fieldset');
        
        // (Html Attribs field collection
        $otherParams = new UserParamsFieldset('other-params-fieldset');
        
        // Add Status values
        $pageStatus = $mixedTermRelFieldset->get('status');
        $pageStatus->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-status',
                'defaultOption' => array(
                    'value' => 'draft',
                    'label' => '-- Select a Page Status --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        // Default the form elements value
        $pageStatus->setValue('published');
        
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
        $pageType = $pageFieldset->get('type');
        $pageType->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'page-type',
                'defaultOption' => array(
                    'value' => 'uri',
                    'label' => '-- Select a Page Type --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));

        // Add Page Category values
        // @todo there should be page categories
        $category = $mixedTermRelFieldset->get('term_taxonomy_id');
        $category->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-category',
                'defaultOption' => array(
                    'value' => null,
                    'label' => '-- Select a Post Category --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_taxonomy_id',
                    'label' => 'term_name'
                )
        )));
        
        // Add Page Fieldset
        $this->add($pageFieldset);
        
        // Add Mixed Term Rel Fieldset
        $this->add($mixedTermRelFieldset);
        
        // Add Menu Page Rel Fieldset
        $this->add($menuPageRelFieldset);
        
        // Add User Params Fieldset
        $this->add($otherParams);
        
        // Add Mvc Params Fieldset
        $this->add($mvc_params);
        
        // Submit and Reset Fieldset
        $this->add(new SubmitAndResetFieldset('submit-and-reset'));
    }

}
