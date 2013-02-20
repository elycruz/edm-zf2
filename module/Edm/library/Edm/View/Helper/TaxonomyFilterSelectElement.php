<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TaxonomyFilterSelectElement
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_TaxonomyFilterSelectElement
    extends Edm_View_Helper_Abstract
{
    public function TaxonomyFilterSelectElement(array $options = null)
    {
        $this->setValidKeyNames(array('taxonomy', 'name', 'value', 'label',
            'firstOptionText', 'lastOptionText', 'attribs'));
//        $this->validateKeyNames($options);
        $this->setOptions($options);
        
        // Set view if necessary
        if (!empty($view)) {
            $this->setView($view);
        }
        
        if (empty($this->lastOptionText)) {
            $this->lastOptionText = 'Show all';
        }

        // Term Tax Service
        $termTaxService = new Edm_Service_Internal_TermTaxonomyService();
        
        // Taxonomies
        $taxonomies = $termTaxService->getTermTaxonomiesByAlias($this->taxonomy,
                        null, 'name', null, null, true);

        $selectBoxOptions = array('0' => '-- '. $this->firstOptionText .' --');
        
        foreach($taxonomies as $row) {
            $selectBoxOptions[$row->term_alias] = !empty($row->parent_id) ?
                    ' - '. $row->term_name : $row->term_name;
        }
        
        $selectBoxOptions = array_replace($selectBoxOptions,
        	array('*' => $this->lastOptionText));
        $output = '';
        $output .= ($this->label ? '<label for="'. $this->name .'">'.
        $this->label .'</label>' : '') . '<br class="cb" />';
        $output .= $this->view->formSelect($this->name, $this->value,
                ($this->attribs ? $this->attribs  : null), $selectBoxOptions);
        return $output;
    }
}
