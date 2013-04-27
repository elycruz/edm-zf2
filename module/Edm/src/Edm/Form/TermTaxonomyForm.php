<?php

namespace Edm\Form;

use Edm\Form\TermFieldset,
    Edm\Form\TermTaxonomyFieldset;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyForm extends EdmForm  {

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
        $termFieldset = new TermFieldset('term');
        $termTaxFieldset = new TermTaxonomyFieldset('term-taxonomy');
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
        // Term Feildset
        $this->add($termFieldset);

        // Term Taxonomy Fieldset
        $this->add($termTaxFieldset);

        // Submit and Reset Fieldset
        $this->add($submitAndReset);
    }
}
