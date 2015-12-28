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
 * @todo change all escape and unescape methods in this class to accept 
 * an options parameter instead of multiple parameters.  Params we need in options
 * array: 'htmlEncodeFields/htmlDecodeFields', 'jsonFields', 'skipFields'
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
     * Notes:
     *    No, ereg_replace is NOT the fastest function ever.
     *    However, it is very UTF-8 safe, which is critcal for
     *    us.  We did some timings, and this took an average of
     *    5x10e-5 seconds per string
     * 
     * Original Code from Mark Wandschneider
     *
     * @param string $str
     * @param bool $in_markup
     * @return string
     */
    public function mega_escape_string($str, $in_markup = false) {
        if ($str === null) {
            return '';
        }
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
    public function reverse_mega_escape_string($str, $in_markup = false) {
        if ($str === null) {
            return '';
        }
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
            else {
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
            else {
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
    protected function reverseEscapeArrayTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if (is_array($skipFields) && in_array($key, $skipFields)) {
                continue;
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple[$key] = $this->reverseEscapeArrayObjectTuple($val, $skipFields, $jsonFields);
            }
            else if (is_array($val)) {
                $tuple[$key] = $this->reverseEscapeArrayTuple($val, $skipFields, $jsonFields);
            }
            else {
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
    protected function reverseEscapeArrayObjectTuple ($tuple, array $skipFields = null, array $jsonFields = null) {
        foreach ($tuple as $key => $val) {
            // Check if field needs to be skipped
            if (is_array($skipFields) && in_array($key, $skipFields)) {
                continue;
            }
            else if (is_array($val)) {
                $tuple->{$key} = $this->reverseEscapeArrayTuple($val);
            }
            else if (is_object($val) && is_a($val, 'ArrayObject')) {
                $tuple->{$key} = $this->reverseEscapeArrayObjectTuple($val);
            }
            else {
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
            $retVal = $this->reverseEscapeArrayTuple($tuple, $skipFields, $jsonFields);
        }
        else if (is_subclass_of($tuple, 'ArrayObject') || get_class($tuple) == 'ArrayObject') {
            $retVal = $this->reverseEscapeArrayObjectTuple($tuple, $skipFields, $jsonFields);
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
    public function jsonEncodeAndEscapeArray ($array, $htmlEntityEncode = false) {
        return $this->mega_escape_string(json_encode($array), $htmlEntityEncode);
    }
    
    /**
     * @param string $string
     * @param bool $htmlEntityDecode
     * @return array
     */
    public function unEscapeAndJsonDecodeString ($string, $htmlEntityDecode = false) {
        return json_decode($this->reverse_mega_escape_string($string, $htmlEntityDecode), true);
    }

}
