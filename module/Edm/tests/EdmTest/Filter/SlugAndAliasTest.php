<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/14/2015
 * Time: 2:38 PM
 */

namespace EdmTest\Filter;

use Edm\Filter\Slug,
    Edm\Filter\Alias;

class SlugAndAliasTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Edm\Filter\Slug
     */
    public static $filterToSlug;

    /**
     * @var \Edm\Filter\Alias
     */
    public static $filterToAlias;

    public static function setUpBeforeClass () {
        self::$filterToSlug = new Slug();
        self::$filterToAlias = new Alias();
    }

    /**
     * Provides data for tests.
     * @return array
     */
    public function filterDataProvider () {
        return [
            [[
                'unfiltered' => 'Hello.  What is your name?',
                'filtered'   => 'hello-what-is-your-name'
            ]],
            [[
                'unfiltered' => 'Thrice as nice!',
                'filtered'   => 'thrice-as-nice'
            ]],
            [[
                'unfiltered' => 'hello%world',
                'filtered'   => 'hello-world'
            ]],
            [[
                'unfiltered' => 'unaffected-value;',
                'filtered'   => 'unaffected-value'
            ]],
            [[
                'unfiltered' => "some' other' value",
                'filtered'   => "some-other-value"
            ]],
            [[
                'unfiltered' => " \\ \\ \\ \\ ",
                'filtered'   => ""
            ]],
            [[
                'unfiltered' => "Not needing escape.",
                'filtered'   => "not-needing-escape"
            ]],
            [[
                'unfiltered' => "All your base are belong to us.",
                'filtered'   => "all-your-base-are-belong-to-us"
            ]],
            [[
                'unfiltered' => ";All ;your ;base ;are ;belong ;to ;us.",
                'filtered'   => "all-your-base-are-belong-to-us"
            ]]
        ];
    }

    public function invalidFilterCandidateProvider () {
        return [
            // To short for test:  Should throw exception
            [ '' ],

            // To long for test;  Should throw exception
            [ str_repeat('A', 201) ],

            // Not of correct type for test;  Should throw exception
            [ 99 ]
        ];
    }

    /**
     * @param $tuple
     * @dataProvider filterDataProvider
     */
    public function testFilter ($tuple) {
        $filter = self::$filterToSlug;
        $this->assertEquals($filter($tuple['unfiltered']), $tuple['filtered']);
    }

    /**
     * @param string $string
     * @dataProvider invalidFilterCandidateProvider
     * @expectedException Exception
     */
    public function testFilterFailsWhenItShould ($string) {
        $filter = self::$filterToSlug;
        $filter($string);
    }

}
