<?php

namespace Edm\Form;

use Edm\Form\PostTermRelFieldset,
    Edm\Form\PostFieldset;

/**
 * Description of PostForm
 * @author ElyDeLaCruz
 */
class PostForm extends EdmForm {

    public function __construct($name = 'post-form', array $options = null) {

        // Set service locator if injected manually
        if (isset($options) && isset($options['serviceLocator'])) {
            $this->setServiceLocator($options['serviceLocator']);
        }

        // Call parent constructor
        parent::__construct($name);

        // Set method
        $this->setAttribute('method', 'post');

        // Post Fieldset
        $postFieldset = new PostFieldset('post-fieldset');
        
        // Post Term Rel Fieldset
        $postTermRelFieldset = new PostTermRelFieldset('post-term-rel-fieldset');
        
        // Add Status values
        $postFieldset->get('status')->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-status',
                'defaultOption' => array(
                    'value' => 'draft',
                    'label' => '-- Select a Post Status --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Add Access Group values
        $postFieldset->get('accessGroup')->setValueOptions(
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
        
        // Add Type values
        $postFieldset->get('type')->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-type',
                'defaultOption' => array(
                    'value' => 'post',
                    'label' => '-- Select a Post Type --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Add Post Category values
        $postTermRelFieldset->get('term_taxonomy_id')->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'post-category',
                'defaultOption' => array(
                    'value' => '0',
                    'label' => '-- Select a Post Category --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Add Commenting values
        $postFieldset->get('commenting')->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'commenting',
                'defaultOption' => array(
                    'value' => '0',
                    'label' => '-- Select a Commenting Status --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Add Post Fieldset
        $this->add($postFieldset);
        
        // Add Post Term Rel Fieldset
        $this->add($postTermRelFieldset);
        
        // Submit and Reset Fieldset
        $this->add(new SubmitAndResetFieldset('submit-and-reset'));
    }

}
