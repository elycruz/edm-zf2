<?php

namespace Edm\Form;

use Edm\Form\UserParamsFieldset,
    Edm\Form\PostTermRelFieldset,
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
        
        // User Params Fieldset
        $userParamsFieldset = new UserParamsFieldset('user-params-fieldset');
        
        // Add Status values
        $postStatus = $postFieldset->get('status');
        $postStatus->setValueOptions(
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
        // Default the form elements value
        $postStatus->setValue('published');
        
        // Add Access Group values
        $accessGroup = $postFieldset->get('accessGroup');
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
        $postType = $postFieldset->get('type');
        $postType->setValueOptions(
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
        
        // Default the post to type of blog
        $postType->setValue('blog');
        
        // Add Post Category values
        $category = $postTermRelFieldset->get('term_taxonomy_id');
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
        
        // Add Commenting values
        $commenting = $postFieldset->get('commenting');
        $commenting->setValueOptions(
            $this->getTaxonomySelectElmOptions(array(
                'taxonomy' => 'commenting-status',
                'defaultOption' => array(
                    'value' => 'disabled',
                    'label' => '-- Select a Commenting Status --'
                ),
                'optionValueAndLabelKeys' => array(
                    'value' => 'term_alias',
                    'label' => 'term_name'
                )
        )));
        
        // Default commenting to enabled
        $commenting->setValue('enabled');
        
        // Add Post Fieldset
        $this->add($postFieldset);
        
        // Add Post Term Rel Fieldset
        $this->add($postTermRelFieldset);
        
        // Add User Params Fieldset
        $this->add($userParamsFieldset);
        
        // Submit and Reset Fieldset
        $this->add(new SubmitAndResetFieldset('submit-and-reset'));
    }

}
