<?php

namespace Edm\TraitPartials;

/**
 * String helpers strToClassCase, strToCamelCase.
 * @author ElyDeLaCruz
 */
trait StringHelpersTrait {

    /**
     * Normalizes a string to a class name.  Also enforces that passed in string
     * should match a pattern ($shouldMatchPattern).  Throws an exception if 
     * string doesn't `should match pattern`.
     * @param String $str
     * @param String $splitOnRegex Regex string
     * @param String $shouldMatchPattern default EDM_ALIAS_PATTERN 
     *          (pattern that string should match)  
     * @return String
     * @throws \Exception
     */
    public function strToClassCase ($str, $splitOnRegex = '/\-/', 
            $shouldMatchPattern = EDM_ALIAS_PATTERN) {
        // Check if value matches the alias pattern
        if (preg_match($shouldMatchPattern, $str) < 0) {
            throw new \Exception(__CLASS__ . '->' . __FUNCTION__ .
            ' requires alias to match the pattern ' . $shouldMatchPattern .
            ' Value received: ' . $str);
        }
        return $this->strToCamelCase($str, true, $splitOnRegex);
    }
    
    public function strToCamelCase ($str, $upperCaseFirst = true, 
            $splitOnRegex = '/\-/') {
        // Return if not a string
        if (!is_string($str)) {
            throw new \Exception(__CLASS__ . '->' . __FUNCTION__ .
            ' requires a string for it\'s param.  Value received: ' .
            $str);
        }

        // Return value
        $retVal = $str;
        
        // Split string
        $parts = preg_split($splitOnRegex);
        
        // Replace dashes
        if (count($parts) > 0) {
            $newParts = array();

            // Loop through parts and upper case first letter
            foreach ($parts as $part) {
                $newParts[] = ucfirst($part);
            }

            // Merge Into Camel Cased String
            $retVal = implode('', $newParts);
        } 
        
        // If upper case first character
        if ($upperCaseFirst) {
            $retVal = ucfirst($str);
        }

        return $retVal;
    }
}
