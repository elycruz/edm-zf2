<?php

namespace Edm\Form;
use Edm\Form\TermFieldset,
Edm\Form\TermTaxonomyFieldset,
Edm\TraitPartials\TermTaxonomyAwareTrait;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyForm extends EdmForm {

    use TermTaxonomyAwareTrait;
    
    public function __construct($name = 'term-taxonomy-form')  {
        parent::__construct($name);
        $this->setAttribute('method', 'post');

        // Create fieldset
        $termFieldset = new TermFieldset('term');
        $termTaxFieldset = new TermTaxonomyFieldset('term-taxonomy');
        $submitAndReset = new SubmitAndResetFieldset('submit-and-reset');
        
//        $termTaxFieldset->get('accessGroup')
//            'value_options' => $this->getTaxonomyOptions('user-group') ));
        
        // Term Feildset
        $this->add($termFieldset);
        
        // Term Taxonomy Fieldset
        $this->add($termTaxFieldset);
        
        // Submit and Reset Fieldset
        $this->add($submitAndReset);

    }
    
    /**
     * Get options for taxonomy select fields
     * @param string $taxonomy
     * @return array
     */
    public function getTaxonomyOptions ($taxonomy) {
        $output = array();
        $rslt = $this->getTermTaxService()->getByTaxonomy($taxonomy, 3);
        foreach ($rslt as $item) {
            $output[$item['term_alias']] = $item['term_name'];
        }
        return $output;
    }

}
