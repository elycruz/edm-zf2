<?php
namespace Edm\Db;
/**
 * Description of DbHelper
 * @todo create a validator/filter for valid html id strings
 * @todo create a validator/filter for valid edm alias 
 *  (aliases used for post, links, terms etc.)
 * @author ElyDeLaCruz
 */
use Edm\Db\DbDataHelper;
class DatabaseDataHelper 
implements DbDataHelper {

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
     *    $in_string          - string to fix up.
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
     * Original idea Mark Wandschneider
     */
    public function mega_escape_string($in_string, $in_markup = FALSE) {
        if ($in_string === NULL)
            return '';
        $str = preg_replace('/(["\'%\;])/', '\\\\\1', $in_string);
        if ($in_markup == TRUE) {
            $str = htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");
        }
        return $str;
    }

    /**
     * The reverse of Edm_Util_DbDataHelper->mega_escape_string()
     * @param <string> $in_string
     * @param <boolean> $in_markup
     * @return <string>
     */
    public function reverse_mega_escape_string($in_string, $in_markup = FALSE) {
        if ($in_string === NULL)
            return '';

        $str = str_replace('\\', '', $in_string);

        if ($in_markup == TRUE) {
            $str = html_entity_decode($str, ENT_NOQUOTES, "UTF-8");
        }
        return $str;
    }

    /**
     * Escapes a tuple for insertion into db
     * @param array $tuple
     * @return array
     */
    public function escapeTuple(array $tuple) {
        foreach ($tuple as $key => $val) {
            $tuple[$key] = $this->mega_escape_string($val);
        }
        return $tuple;
    }

    /**
     * Reverse escape a collection of rows/tuples
     * @param array $tuples
     * @return array
     */
    public function escapeTuples(array $tuples) {
        $new_array = array();
        /**
         * Loop through rows and escape them for our view
         */
        foreach ($tuples as $key => $tuple) {
            $new_array[$key] = $this->escapeTuple($tuple);
        }
        return $new_array;
    }

    /**
     * Un-escapes our values from our db via the Service layer
     * @param array $values
     * @return array
     */
    public function reverseEscapeTuple(array $tuple) {
        foreach ($tuple as $key => $val) {
            $tuple[$key] = $this->reverse_mega_escape_string($val);
        }
        return $tuple;
    }

    /**
     * Reverse escape a collection of rows/tuples
     * @param array $tuples
     * @return array
     */
    public function reverseEscapeTuples(array $tuples) {
        $new_array = array();
        /**
         * Loop through rows and escape them for our view
         */
        foreach ($tuples as $tuple) {
            $new_array[] = $this->reverseEscapeTuple(
                            $tuple
            );
        }
        return $new_array;
    }

    /**
     * Takes a string and returns a valid alias (can be used for xml nodeName
     * and other none space qualifying string)
     * @param String $string
     * @return String /^[\-\_a-z\d]{5,255}$/ to lower case
     */
    public function getValidAlias($str) {
        if (strlen($str) < 255 && strlen($str) > 0) {
            $str = trim($str);
            $str = preg_replace('/[^\-\w\d_]/i', '-', $str);
            $str = strtolower($str);
            return $str;
        } else {
            throw new Exception('Valid `Aliases` must be less than 255 ' .
                    'Characters in length.');
        }
    }
}
