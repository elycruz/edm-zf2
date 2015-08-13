<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/12/2015
 * Time: 11:27 PM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap;


class TermTaxonomyServiceTest extends \PHPUnit_Framework_TestCase {

    use \Edm\Service\TermTaxonomyServiceAwareTrait,
        \Edm\ServiceManager\ServiceLocatorAwareTrait;

    protected function setUp() {
        $this->setServiceLocator(Bootstrap::getServiceManager());
    }

    public function testTermTaxonomyLayer() {
        $this->assertCount(10,
            $this->termTaxonomyService()
                 ->getByAlias('taxonomy'));
    }

    public function testDbConnection () {
        $dbAdapter = $this->userService->getDb();
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $dbAdapter);
        $schema = $dbAdapter->getDriver()->getConnection()->getCurrentSchema();
        $this->assertEquals('edm', $schema);
    }

    protected function tearDown () {

    }
}