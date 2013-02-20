<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_TupleAuthorName
    extends Edm_View_Helper_Abstract
{
    public function tupleAuthorName($options = null, $view = null)
    {
        /**
         * Set valid key names for options if not already set
         */
        $validKeyNames = $this->getValidKeyNames();
        if (empty($validKeyNames)) {
            $this->setValidKeyNames(array('id'));
        }

        /**
         * Set our view so that we could use the predefined values in it for
         * this view helper instead of using the $options
         */
        if (!empty($view)) {
            $this->setView($view);
        }

        /**
         *  Validate our options and make them available via $this->optionName
         */
        if (!empty($options)) {
            $this->validateKeyNames($options);
            $this->setOptions($options);
        }

        $userService = new Edm_Service_Internal_UserService();
        $author = $userService->getUserById($this->id);
        
        /**
         * Prepare output
         */
        $output = $author['firstName'] .' '. $author['lastName'];

        /**echo '<pre>';
        var_dump($search);
        echo '</pre>'; exit();*/
        return $output;
    }
}
