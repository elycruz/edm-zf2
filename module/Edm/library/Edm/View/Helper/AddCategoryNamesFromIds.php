<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of addCategoryNamesFromIds
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_AddCategoryNamesFromIds extends Edm_View_Helper_Abstract
{
    public function addCategoryNamesFromIds($options)
    {
        /**
         * Set valid key names for options if not already set
         */
        $validKeyNames = $this->getValidKeyNames();
        if (empty($validKeyNames)) {
            $this->setValidKeyNames(array('tuples'));
        }

        /**
         *  Validate our options
         */
        if (!empty($options)) {
            $this->validateKeyNames($options);
            /**
             * Set options and make them available via $this->optionName
             */
            $this->setOptions($options);
        }

        /**
         * Get our Section service and our sections array
         */
        $service = new Edm_Service_Internal_CategoryService();
        $categories = $service->getAllMultiTableModels(
                $this->sortBy, $this->sort);

        /**
         * Prepare output
         */
        $output = array();

        foreach($this->tuples as $tuple) {
            foreach ($categories as $category) {
                if ($tuple['term_taxonomy_id'] == $category['id']) {
                    $tuple['categoryName'] = $category['name'];
                    $output[] = $tuple;
                }
            }
        }

        return $output;
    }
}