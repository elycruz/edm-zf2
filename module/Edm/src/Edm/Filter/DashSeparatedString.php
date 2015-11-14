<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/14/2015
 * Time: 2:07 PM
 */

namespace Edm\Filter;

use Zend\Filter\FilterInterface;

/**
 * Class DashSeparatedString
 * Filters a string with of 1-200 characters in length and conforms it
 * to a valid 'dash-separated' string or a valid alias/url-slug formatted string.
 * @note Can be called as a function.
 * @author Ely De La Cruz <elycruz - at - elycruz - dot - com>
 * @package Edm\Filter
 */
class DashSeparatedString implements FilterInterface {

    /**
     * Calls `filter` method and filters string.
     * @param string $value
     * @return string
     * @throws \Exception
     */
    public function __invoke($value) {
        return $this->filter($value);
    }

    /**
     * Returns a string as a valid 'dash-separated' string or as a valid alias/url-slug.
     * @param string $value - String to filter (must be 1-200 characters in length).
     * @return string - Filtered string.
     * @throws \Exception - Throws an exception if type is not of 'string' or if passed in string is not 1 to 200 characters.
     */
    public function filter ($value) {
        $filterName = '`' . __CLASS__ . '->' . __FUNCTION__ . '`';
        if (!is_string($value)) {
            throw new \Exception($filterName . ' only accepts strings.  Value received: ' . $value);

        }
        else if (is_string($value) && strlen($value) <= 200 && strlen($value) > 0) {
            $value = preg_replace('/[^\-a-z\d\_]/i', '-', strtolower(trim(trim($value), '-')));
        }
        else {
            throw new \Exception($filterName .' requires valid candidate ' .
                '`dash-separated` strings to be 0-200 ' .
                'characters in length.  Value received: ' . $value);
        }
        return $value;
    }
}