<?php

namespace Edm\Form;

use Edm\Form\TermFieldset,
    Edm\Form\TermTaxonomyFieldset,
    Edm\Service\TermTaxonomyService,
        Edm\Service\AbstractService;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyForm extends EdmForm {
    
    public $termTaxService;

    public function __construct($name = 'term-taxonomy-form', array $options = null) {
        
        // Set service locator if injected manually
        if (isset($options) &&isset($options['serviceLocator'])) {
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

        $termTaxFieldset->get('taxonomy')->setOptions(array(
            'value_options' => $this->getTaxonomyOptions('taxonomy') 
        ));
        
//        var_dump($this->getTaxonomyOptions('taxonomy'));

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
    public function getTaxonomyOptions($taxonomy) {
        $output = array();
        $rslt = $this->getTermTaxService()->getByTaxonomy('taxonomy', array(
            'fetchMode' => AbstractService::FETCH_RESULT_SET_TO_ARRAY,
            'nestedResults' => true,
            'order' => 'term_name ASC',
//            'where' => array('term_alias' => 'testing-9')
        ));
        $output['null'] = '-- Select a Taxonomy --';
        $output['taxonomy'] = 'Taxonomy';
        foreach ($rslt as $item) {
            $output[$item['term_alias']] = $item['term_name'];
        }
        return $output;
    }

    public function getTermTaxService() {
        if (empty($this->termTaxService)) {
            $this->termTaxService = 
                    $this->serviceLocator
                        ->get('Edm\Service\TermTaxonomyService');
        }
        return $this->termTaxService;
    }

}
