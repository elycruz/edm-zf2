<?php

declare(strict_types=1);

namespace Edm\Db;

use \ArrayObject;

/**
 * DbDataHelper helps with escaping values to RDBMS and unescaping values
 * from the RDBMS. 
 * @todo create a validator/filter for valid html id strings
 * @todo create a validator/filter for valid edm alias 
 *  (aliases used for post, links, terms etc.)
 * @note the nested use of mega_escape_string and reverse_mega_escape_string
 * found in this class do not accept the decode/encode html entities param (
 * they only accept that param directly as we don't want the service layer
 * controlling view logic (or deciding how things should look to in the view
 * layer).
 * @author ElyDeLaCruz
 */

class DbDataHelper implements DbDataHelperInterface {

    /**
     * =---------------------------------------------------------=
     * mega_escape_string
     * =---------------------------------------------------------=
     * This is a much improved version of a string manipulator
     * that makes strings safe for:
     *
     * - insertion into our database.
     * - subsequent display in our pages.
     *
     * Specifically, this function:
     *    - prefixes all ', %, and ; characters with
     *        backslashes
     *    - optionally replaces all < and > characters with
     *        the appropriate entity (&lt; and &gt;).
     *
     * Parameters:
     *    $str          - string to fix up.
     *    $in_markup          - [optional] replace HTML markup
     *                            < and > ???
     *
     * Returns:
     *    string -- safe!!!!!
     *
     * Original Code from Mark Wandschneider
     *
     * @param string $str
     * @param bool $in_markup
     * @return string
     */
    public function mega_escape_string(string $str, bool $in_markup = false) {
        $str = preg_replace('/(\\\\)/', '%5C', $str); // backslash needs to be doubled in the regex for it to work
        $str = preg_replace('/(["\'%;])/', '\\\\$1', $str);
        if ($in_markup == true) {
            $str = htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");
        }
        return $str;
    }

    /**
     * The reverse of `mega_escape_string` method
     * @param string $str
     * @param boolean $in_markup
     * @return string
     */
    public function reverse_mega_escape_string(string $str, bool $in_markup = false) {
        $str = str_replace('\\', '', $str);
        $str = str_replace('%5C', '\\', $str);
        if ($in_markup == true) {
            $str = html_entity_decode($str, ENT_NOQUOTES, "UTF-8");
        }
        return $str;
    }

    /**
     * @param array $tuple
     * @param null|array $skipFields - Fields to not escape.
     * @param null | array $jsonFields
     * @return array - Escaped $tuple.
     */
    protected function _escapeArrayTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        $isPopulatedSkipFields = !empty($skipFields);
        $isPopulatedJsonFields = !empty($jsonFields);
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if ($isPopulatedSkipFields && in_array($key, $skipFields)) {
                continue;
            }
            if ($isPopulatedJsonFields && in_array($key, $jsonFields)) {
                $tuple[$key] = $this->jsonEncodeAndEscapeArray($val);
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple[$key] = $this->_escapeArrayObjectTuple($val, $skipFields, $jsonFields);
            }
            else if (is_array($val)) {
                $tuple[$key] = $this->_escapeArrayTuple($val, $skipFields, $jsonFields);
            }
            else if (is_string($val)) {
                $tuple[$key] = $this->mega_escape_string($val);
            }
        }
        return $tuple;
    }

    /**
     * @param ArrayObject $tuple
     * @param null|array $skipFields - Fields to not escape.
     * @param null | array $jsonFields
     * @return ArrayObject - Escaped $tuple.
     */
    protected function _escapeArrayObjectTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        $isPopulatedSkipFields = !empty($skipFields);
        $isPopulatedJsonFields = !empty($jsonFields);
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if ($isPopulatedSkipFields && in_array($key, $skipFields)) {
                continue;
            }
            if ($isPopulatedJsonFields && in_array($key, $jsonFields)) {
                $tuple->{$key} = $this->jsonEncodeAndEscapeArray($val);
            }
            else if (is_array($val)) {
                $tuple->{$key} = $this->_escapeArrayTuple($val);
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple->{$key} = $this->_escapeArrayObjectTuple($val);
            }
            else if (is_string($val)) {
                $tuple->{$key} = $this->mega_escape_string($val);
            }
        }
        return $tuple;
    }

    /**
     * Escapes a tuple for insertion into db
     * @param array|ArrayObject $tuple - array, ArrayObject
     * @param null | array $skipFields
     * @param null | array $jsonFields
     * @throws Exception if $tuple type(s) are not matched.
     * @return array|ArrayObject - Escaped $tuple.
     */
    public function escapeTuple($tuple, array $skipFields = null, array $jsonFields = null) {
        if (is_array($tuple)) {
            $retVal = $this->_escapeArrayTuple($tuple, $skipFields, $jsonFields);
        }
        else if (is_subclass_of($tuple, 'ArrayObject') || get_class($tuple) == 'ArrayObject') {
            $retVal = $this->_escapeArrayObjectTuple($tuple, $skipFields, $jsonFields);
        }
        else {
            throw new Exception('`' . __CLASS__ . '->' . __FUNCTION__ . '` expects a `$tuple` parameter of type ' .
                'either, subclass or class of `\ArrayObject` or of type `array`.  Value received: ' . $tuple);
        }
        return $retVal;
    }

    /**
     * Reverse escape a collection of rows/tuples
     * @param array $tuples
     * @param array $skipFields - Fields to skip or not escape.
     * @param null | array $jsonFields
     * @return array - Array of escaped tuples<array|ArrayObject>.
     */
    public function escapeTuples($tuples, array $skipFields = null, array $jsonFields = null) {
        $new_array = array();
        // Loop through rows and escape them for our view
        foreach ($tuples as $tuple) {
            $new_array[] = $this->escapeTuple($tuple, $skipFields, $jsonFields);
        }
        return $new_array;
    }

    /**
     * @param array $tuple
     * @param null|array $skipFields - Fields to not reverse-escape.
     * @param null | array $jsonFields
     * @return array $tuple
     */
    protected function _reverseEscapeArrayTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        $isPopulatedSkipFields = !empty($skipFields);
        $isPopulatedJsonFields = !empty($jsonFields);
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if ($isPopulatedSkipFields && in_array($key, $skipFields)) {
                continue;
            }
            if ($isPopulatedJsonFields && in_array($key, $jsonFields)) {
                $tuple[$key] = $this->unEscapeAndJsonDecodeString($val);
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple[$key] = $this->_reverseEscapeArrayObjectTuple($val, $skipFields, $jsonFields);
            }
            else if (is_array($val)) {
                $tuple[$key] = $this->_reverseEscapeArrayTuple($val, $skipFields, $jsonFields);
            }
            else if (is_string($val)) {
                $tuple[$key] = $this->reverse_mega_escape_string($val);
            }
        }
        return $tuple;
    }

    /**
     * @param ArrayObject $tuple
     * @param null|array $skipFields - Fields to not reverse-escape.
     * @param null | array $jsonFields
     * @return ArrayObject $tuple
     */
    protected function _reverseEscapeArrayObjectTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        $isPopulatedSkipFields = !empty($skipFields);
        $isPopulatedJsonFields = !empty($jsonFields);
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if ($isPopulatedSkipFields && in_array($key, $skipFields)) {
                continue;
            }
            if ($isPopulatedJsonFields && in_array($key, $jsonFields)) {
                $tuple->{$key} = $this->unEscapeAndJsonDecodeString($val);
            }
            else if (is_array($val)) {
                $tuple->{$key} = $this->_reverseEscapeArrayTuple($val, $skipFields, $jsonFields);
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple->{$key} = $this->_reverseEscapeArrayObjectTuple($val, $skipFields, $jsonFields);
            }
            else if (is_string($val)) {
                $tuple->{$key} = $this->reverse_mega_escape_string($val);
            }
        }
        return $tuple;
    }

    /**
     * Un-escapes our values from our db via the Service layer
     * @param array|ArrayObject $tuple - Also subclass of ArrayObject allowed.
     * @param null|array $skipFields - Fields to not reverse-escape.
     * @param null | array $jsonFields
     * @throws Exception - Throws exception when $tuple type(s) are not matched.
     * @return array
     */
    public function reverseEscapeTuple($tuple, array $skipFields = null, array $jsonFields = null) {
        if (is_array($tuple)) {
            $retVal = $this->_reverseEscapeArrayTuple($tuple, $skipFields, $jsonFields);
        }
        else if (is_subclass_of($tuple, 'ArrayObject') || get_class($tuple) == 'ArrayObject') {
            $retVal = $this->_reverseEscapeArrayObjectTuple($tuple, $skipFields, $jsonFields);
        }
        else {
            throw new Exception('`' . __CLASS__ . '->' . __FUNCTION__ . '` expects a `$tuple` parameter of type ' .
                'either, subclass or class of `\ArrayObject` or of type `array`.  Value received: ' . $tuple);
        }
        return $retVal;
    }

    /**
     * Reverse escape a collection of rows/tuples
     * @param array $tuples
     * @param null|array $skipFields - Fields to not reverse-escape.
     * @param null | array $jsonFields
     * @return array
     */
    public function reverseEscapeTuples($tuples, array $skipFields = null, array $jsonFields = null) {
        $new_array = array();
        // Loop through rows and escape them for our view
        foreach ($tuples as $tuple) {
            $new_array[] = $this->reverseEscapeTuple($tuple, $skipFields, $jsonFields);
        }
        return $new_array;
    }
    
    /**
     * @param array $array
     * @param bool $htmlEntityEncode
     * @return string
     */
    public function jsonEncodeAndEscapeArray (array $array, bool $htmlEntityEncode = false) {
        return $this->mega_escape_string(json_encode($array), $htmlEntityEncode);
    }
    
    /**
     * @param string $string
     * @param bool $htmlEntityDecode
     * @return array
     */
    public function unEscapeAndJsonDecodeString (string $string, bool $htmlEntityDecode = false) {
        return json_decode($this->reverse_mega_escape_string($string, $htmlEntityDecode), true);
    }

}
