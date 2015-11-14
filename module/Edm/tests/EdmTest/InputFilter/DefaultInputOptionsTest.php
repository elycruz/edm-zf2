<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/13/2015
 * Time: 3:44 PM
 * Description: These tests just check for expected keys in config
 * (@see ./module/Edm/configs/default.input.options.php);
 */

namespace EdmTest\InputFilter;

use Edm\InputFilter\DefaultInputOptions;

class DefaultInputOptionsTest extends \PHPUnit_Framework_TestCase  {

    public static $defaultInputOptions;

    public $expectedKeys = [
        'id', 'int', 'alias', 'short-alias', 'name', 'short-name',
        'boolean', 'email', 'password', 'html_id', 'html_class',
        'description', 'screen-name', 'activation-key'
    ];

    public static function setUpBeforeClass () {
        self::$defaultInputOptions = new DefaultInputOptions();
    }

    function testHasExpectedKeys () {
        $defaultInputOptions = self::$defaultInputOptions;
        foreach ($this->expectedKeys as $key) {
            $this->assertEquals(true, $defaultInputOptions->offsetExists($key));
        }
    }
}