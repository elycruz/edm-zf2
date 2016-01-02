<?php
/**
 * Created by IntelliJ IDEA.
 * UserParams: Ely
 * Date: 11/18/2015
 * Time: 4:31 PM
 */

namespace EdmTest\Form\Fieldset;

use Edm\Form\Fieldset\UserParamsFieldset;

class UserParamsFieldsetTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldNames = [
        'screenName',
        'password',
        'status',
        'role',
        'accessGroup'
    ];
    
    /**
     * @return UserParamsFieldset
     */
    public function userParamsFieldsetProvider () {
        return [
            [new UserParamsFieldset()],
            [new UserParamsFieldset('user-params')]
        ];
    }

    /**
     * @dataProvider userParamsFieldsetProvider
     */
    public function testExistence ($userParamsFieldset) {
        $this->assertInstanceOf('Edm\Form\Fieldset\UserParamsFieldset', $userParamsFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $userParamsFieldset);
        var_dump($userParamsFieldset->get('userParams'));
    }

    /**
     * @dataProvider userParamsFieldsetProvider
     * @param UserParamsFieldset $fieldset
     */
//    public function testHasExpectedFields ($fieldset) {
//        // Check required fields
//        foreach ($this->fieldNames as $fieldName) {
//            $this->assertEquals(true, $fieldset->has($fieldName));
//        }
//    }

    /**
     * @dataProvider userParamsFieldsetProvider
     * @param UserParamsFieldset $fieldset
     */
//    public function testExpectedFieldTypes ($fieldset) {
//        foreach ($this->fieldNames as $fieldName) {
//            $field = $fieldset->get($fieldName);
//            $this->assertInstanceOf('Zend\Form\Element', $field);
//        }
//    }

}
