<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/23/2015
 * Time: 3:32 PM
 * @note Currently this test case class is serving as test case for both 'UserProto' and 'AbstractProto'.
 * @note test cases should be separated.
 * @todo Separate the `AbstractProto` tests' stuff from this test case.
 * @todo Use mock objects for `AbstractProto` tests.
 */

namespace EdmTest\Db\ResultSet\Proto;

use Edm\Db\ResultSet\Proto\UserProto;

class UserProtoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    public $allowedKeysForProto = [
        'user_id',
        'screenName',
        'password',
        'role',
        'accessGroup',
        'status',
        'lastLogin',
        'activationKey',
        'date_info_id'
    ];

    /**
     * @var string
     */
    public $formKey = 'user';

    /**
     * @return array<[UserProto]>
     */
    public function emptyUserProvider () {
        return [[new UserProto()]];
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetFormKey (UserProto $user) {
        $this->assertEquals($this->formKey, $user->getFormKey());
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetAllowedKeysForProto (UserProto $user) {
        $this->assertArraySubset($this->allowedKeysForProto,
            $user->getAllowedKeysForProto());
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetSubProtoGetters (UserProto $user) {
        $this->assertArraySubset(['getDateInfoProto', 'getContactProto'],
            $user->getSubProtoGetters());
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetContactProto (UserProto $user) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\ContactProto',
            $user->getContactProto());
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetDateInfoProto (UserProto $user) {
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\DateInfoProto',
            $user->getDateInfoProto());
    }

    /**
     * @dataProvider emptyUserProvider
     * @param $user
     */
    public function testGetInputFilter (UserProto $user) {
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface',
            $user->getInputFilter());
    }

}
