<?php

namespace EdmTest\Controller;

use EdmTest\Bootstrap;
use \PHPUnit_Framework_TestCase;

/**
 * Description of TermTaxonomyLayerTest
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyLayerTest extends PHPUnit_Framework_TestCase {

    protected $serviceLocator;

    protected function setUp() {
        $this->serviceLocator = Bootstrap::getServiceManager();
    }

    public function testTermTaxonomyLayer() {
        $db = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
        var_dump($db->query('SELECT * FROM terms'));
    }
}