<?php
/**
 * Description of ArrayHelper
 *
 * @author ElyDeLaCruz
 */
class Edm_Util_ArrayHelper
{
        /**
     * Utility function to check for a difference between two arrays.
     *
     * @param  array $options      User specified options
     * @param  array $validOptions Valid options
     * @return void
     * @throws Zend_Service_Exception if difference is found
     * (e.g., unsupported query option)
     */
    public static function compareKeys(array $options, array $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if ($difference) {
            throw new Zend_Service_Exception('The following parameters are '.
                    'invalid: ' . join(', ', $difference));
        }
    }


    /**
     * Check that a named value is in the given array
     *
     * @param  string $name  Name associated with the value
     * @param  mixed  $value Value
     * @param  array  $array Array in which to check for the value
     * @return void
     * @throws Zend_Service_Exception
     */
    public static function validateInArray($name, $value, array $array)
    {
        if (!in_array($value, $array)) {
            throw new Zend_Service_Exception(
                    "Invalid value for option '$name': $value");
        }
    }

}