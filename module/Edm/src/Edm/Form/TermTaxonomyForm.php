<?php

namespace Edm\Form;
use Edm\Form\TermFieldset;
use Edm\Form\TermTaxonomyFieldset;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyForm extends EdmForm {

    public function __construct() {
        parent::__construct('term-taxonomy-form');
        $this->setAttribute('method', 'post');

        // Create fieldset
        $termFieldset = new TermFieldset('term');
        $termTaxFieldset = new TermTaxonomyFieldset('term-taxonomy');
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');
        
        // Term Feildset
        $this->add($termFieldset);
        
        // Term Taxonomy Fieldset
        $this->add($termTaxFieldset);
        
        // Submit and Reset Fieldset
        $this->add($submitAndReset);

    }

}
