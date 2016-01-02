<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 4:31 PM
 */

namespace EdmTest\Form\Fieldset;

use Edm\Form\Fieldset\UserFieldset;

class UserFieldsetTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldNames = [
        'screenName',
        'password',
        'status',
        'role',
        'accessGroup'
    ];
    
    /**
     * @return UserFieldset
     */
    public function userFieldsetProvider () {
        return [
            [new UserFieldset()],
            [new UserFieldset('user')]
        ];
    }

    /**
     * @dataProvider userFieldsetProvider
     */
    public function testExistence ($userFieldset) {
        $this->assertInstanceOf('Edm\Form\Fieldset\UserFieldset', $userFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $userFieldset);
    }

    /**
     * @dataProvider userFieldsetProvider
     * @param UserFieldset $fieldset
     */
    public function testHasExpectedFields ($fieldset) {
        // Check required fields
        foreach ($this->fieldNames as $fieldName) {
            $this->assertEquals(true, $fieldset->has($fieldName));
        }
    }

    /**
     * @dataProvider userFieldsetProvider
     * @param UserFieldset $fieldset
     */
    public function testExpectedFieldTypes ($fieldset) {
        foreach ($this->fieldNames as $fieldName) {
            $field = $fieldset->get($fieldName);
            $this->assertInstanceOf('Zend\Form\Element', $field);
        }
    }

    /**
     * @dataProvider userFieldsetProvider
     * @param UserFieldset $fieldset
     */
    public function testProtoObjectType ($fieldset) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\UserProto', 
                $fieldset->getObject());
    }

}
