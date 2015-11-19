<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 4:31 PM
 */

namespace EdmTest\Form\Fieldset;

use Edm\Form\Fieldset\TermFieldset;

class TermFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return TermFieldset
     */
    public function termFieldsetProvider () {
        return [
            [new TermFieldset()],
            [new TermFieldset('term')]
        ];
    }

    /**
     * @dataProvider termFieldsetProvider
     */
    public function testExistence () {
        $fieldset = new TermFieldset();
        $this->assertInstanceOf('Edm\Form\Fieldset\TermFieldset', $fieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $fieldset);
    }

    /**
     * @dataProvider termFieldsetProvider
     * @param TermFieldset $fieldset
     */
    public function testHasExpectedFields ($fieldset) {
        // Check required fields
        $this->assertEquals($fieldset->has('name'), true);
        $this->assertEquals($fieldset->has('alias'), true);
        $this->assertEquals($fieldset->has('term_group_alias'), true);
    }

    /**
     * @dataProvider termFieldsetProvider
     * @param TermFieldset $fieldset
     */
    public function testExpectedFieldTypes ($fieldset) {
        // Fetch fields
        $nameElm = $fieldset->get('name');
        $aliasElm = $fieldset->get('alias');
        $termGroupAliasElm = $fieldset->get('term_group_alias');

        // Assert field types
        $this->assertInstanceOf('Zend\Form\Element', $nameElm);
        $this->assertInstanceOf('Zend\Form\Element', $aliasElm);
        $this->assertInstanceOf('Zend\Form\Element', $termGroupAliasElm);
    }

    /**
     * @dataProvider termFieldsetProvider
     * @param TermFieldset $fieldset
     */
    public function testProtoObjectType ($fieldset) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\TermProto', $fieldset->getObject());
    }

}
