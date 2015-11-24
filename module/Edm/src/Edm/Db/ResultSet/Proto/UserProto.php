<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class UserProto extends AbstractProto {

    /**
     * User role.
     * @var {string}
     */
    public $role = 'cms-manager';
    
    /**
     * User status.
     * @var {string}
     */
    public $status = 'pending-activation';
    
    /**
     * User's Access Group.
     * @var {String}
     */
    public $accessGroup = 'cms-manager';
    
    /**
     * Valid keys for model
     * @var array
     */
    protected $_allowedKeysForProto = array(
        'user_id',
        'screenName',
        'password',
        'role',
        'accessGroup',
        'status',
        'lastLogin',
        'activationKey',
        'date_info_id'
    );
    
    /**
     * Keys not allowed for update.
     * @var array
     */
    protected $_notAllowedKeysForUpdate = array(
        'activationKey',
        'date_info_id',
        'user_id'
    );

    /**
     * @var string
     */
    protected $_formKey = 'user';

    /**
     * @var array
     */
    protected $_subProtoGetters = [
        'getDateInfoProto',
        'getContactProto'
    ];

    /**
     * Contact Proto Object
     * @var ContactProto
     */
    protected $contactProto;
    
    /**
     * Date Info Proto.
     * @var DateInfoProto
     */
    protected $dateInfoProto;

    /**
     * Returns our input filter.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter() {

        // Return input filter if exists
        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }

        // Return value (input filter)
        $inputFilter = new InputFilter();

        // Input factory
        $factory = new InputFactory();

        // Screen Name
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('screen-name', array(
                'name' => 'screenName',
                'required' => false
        ))));

        // Password
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('password', array(
                'name' => 'password',
                'required' => true
        ))));

        // Role
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'role',
                    'required' => false
        ))));

        // Access Group
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'accessGroup',
                    'required' => false
        ))));
        
        // Status
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'status',
                    'required' => false
        ))));

        // Activation Key
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('activation-key', array(
                    'name' => 'activationKey',
                    'required' => false
        ))));
        
        // Last Login
        // Date Info Id

        // Set input filter
        $this->_inputFilter = $inputFilter;

        // Return input filter
        return $inputFilter;
    }
    
    /**
     * Gets Contact Proto
     * @param array|null $data - Default `null`.
     * @return ContactProto
     */
    public function getContactProto($data = null) {
        if (empty($this->contactProto)) {
            $this->contactProto = new ContactProto($data);
        }
        return $this->contactProto;
    }
    
    /**
     * Gets our Date Info Proto.
     * @param array|null $data - Default `null`.
     * @return DateInfoProto
     */
    public function getDateInfoProto($data = null) {
        if (empty($this->dateInfoProto)) {
            $this->dateInfoProto = new DateInfoProto($data);
        }
        return $this->dateInfoProto;
    }
    
}
