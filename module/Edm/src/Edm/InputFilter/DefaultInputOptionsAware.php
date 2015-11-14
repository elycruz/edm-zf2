<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/13/2015
 * Time: 4:11 PM
 */

namespace Edm\InputFilter;

interface DefaultInputOptionsAware {
    public static function getDefaultInputOptions ();
    public static function getDefaultInputOptionsByKey ($key, array $defaults);
}