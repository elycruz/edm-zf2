<?php

namespace EdmTest\Controller;

use EdmTest\Bootstrap;
use \PHPUnit_Framework_TestCase,
    Edm\Service\TermTaxonomyService;

/**
 * Description of TermTaxonomyLayerTest
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyLayerTest extends PHPUnit_Framework_TestCase {

    protected $serviceLocator;
    protected $service;

    protected function setUp() {
        $this->serviceLocator = Bootstrap::getServiceManager();
        $this->service = new TermTaxonomyService($this->serviceLocator);
    }

    public function testTermTaxonomyLayer() {
        $this->assertCount(10, $this->service->getByAlias('taxonomy'));
    }
}