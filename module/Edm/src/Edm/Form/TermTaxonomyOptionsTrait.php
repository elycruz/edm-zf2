<?php

namespace Edm\Form;

use Zend\Config\Config;

/**
 * Description of TermTaxonomyOptionsTrait
 *
 * @author ElyDeLaCruz
 */
trait TermTaxonomyOptionsTrait 
{
    /**
     * Generates select options for taxonomy select elements.
     * @param mixed array | \Zend\Config\Config $options default null;
     * $options' keys (optional):
     *      string taxonomy
     *      array defaultOption
     *          mixed value
     *          string label
     *      array optionValueAndLabelKeys
     *          string value
     *          string label
     * @return array
     */
    protected function getTaxonomySelectElmOptions($options = null) {
        // Default options
        $defaultOptions = array(
            'taxonomy' => 'taxonomy',
            'defaultOption' => array(
                'value' => 'taxonomy',
                'label'  => '-- Select a Taxonomy --'
            ),
            'optionValueAndLabelKeys' => array(
                'value' => 'term_alias',
                'label' => 'term_name'
            ),
        );
        
        // If options not config object
        if (isset($options) && !is_array($options)) {
            if (is_array($options)) {
                $options = new Config(array_replace_recursive($defaultOptions, $options));
            }
        }
        else {
            $options = new Config($defaultOptions);
        }
        
        // Read options
        $readOptions = array(
            'nestedResults' => true,
            'order' => 'term_name ASC'
        );
        
        // If taxonomy set it in read options
        $taxonomy = $options->taxonomy;
        if (!empty($taxonomy)) {
            $readOptions['where'] = array('taxonomy' => $taxonomy);
        }
        
        // Fetch taxonomies 
        $output = array();
        $rslt = $this->termTaxonomyService()->read($readOptions);
        
        // Set the default option
        if (isset($options->defaultOption)) {
            $output[$options->defaultOption->value] = 
                    $options->defaultOption->label;
        }
        
        // If default option value equal to taxonomy add taxonomy to the list
        // @todo this is a temporary fix
        if ($options->taxonomy === 'taxonomy') {
            $output['taxonomy'] = 'Taxonomy';
        }

        // Compose select element html options data
        foreach ($rslt as $item) {
            $output[$item[$options->optionValueAndLabelKeys->value]] = 
                    $item[$options->optionValueAndLabelKeys->label];
        }
        
        // Return output
        return $output;
    }
}
