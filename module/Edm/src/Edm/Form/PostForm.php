<?php

namespace Edm\Form;

use Edm\Form\PostTermRelFieldset,
    Edm\Form\PostFieldset;

/**
 * Description of TermForm
 *
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

        // Create fieldset
        $termFieldset = new TermFieldset('term');
        $termTaxFieldset = new PostFieldset('post');
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');

        // Set value options for taxonomy
        $termTaxFieldset->get('taxonomy')->setValueOptions(
                $this->getTaxonomySelectElmOptions());

        // Set value options for parent_id
        $termTaxFieldset->get('parent_id')->setValueOptions(
                $this->getTaxonomySelectElmOptions(array(
                    'taxonomy' => null,
                    'defaultOption' => array(
                        'value' => '0',
                        'label' => '-- Select a Parent (optional) --'
                    ),
                    'optionValueAndLabelKeys' => array(
                        'value' => 'term_taxonomy_id',
                        'label' => 'term_name'
                    )
        )));

        // Set value options for parent_id
        // Post Term Rel Feildset
        $this->add($termFieldset);

        // Post Term Rel Taxonomy Fieldset
        $this->add($termTaxFieldset);

        // Submit and Reset Fieldset
        $this->add($submitAndReset);
    }

}
