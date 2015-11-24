<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/24/2015
 * Time: 3:16 PM
 */

namespace EdmTest\Db\ResultSet\Proto;


use Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto;

class TermTaxonomyProxyProtoTest extends \PHPUnit_Framework_TestCase {

    public function testGetAllowedKeysForProto () {
        $proto = new TermTaxonomyProxyProto();
        $this->assertArraySubset([
                'term_taxonomy_id',
                'assocItemCount',
                'childCount'
            ],
            $proto->getAllowedKeysForProto());
    }

    public function testFormKey () {
        $proto = new TermTaxonomyProxyProto();
        $this->assertEquals('termTaxonomyProxy', $proto->getFormKey());
    }

}
