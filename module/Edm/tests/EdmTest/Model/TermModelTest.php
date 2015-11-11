<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/11/2015
 * Time: 1:35 PM
 */

namespace EdmTest\Model;

use EdmTest\Bootstrap,
    Edm\ServiceManager\ServiceLocatorAwareTrait,
    Edm\Model\Term;

class TermModelTest extends \PHPUnit_Framework_TestCase {

    public $validFields = [
        'alias',
        'name',
        'term_group_alias'
    ];

}
