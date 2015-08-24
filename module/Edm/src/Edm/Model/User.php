<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

class User extends AbstractModel implements InputFilterAwareInterface, ModelInterface {

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
    public $validKeys = array(
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
    public $notAllowedForUpdate = array(
        'activationKey',
        'date_info_id',
        'user_id'
    );

    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;
    
    /**
     * Contact Proto Object
     * @var Edm\Model\Contact
     */
    protected $contactProto;
    
    /**
     * Date Info Proto.
     * @var Edm\Model\DateInfo
     */
    protected $dateInfoProto;

    /**
     * Constructor
     * @param {mixed|Array|numm} $data - default null
     */
    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }

    /**
     * Sets our input filter.
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @return null;
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    /**
     * Returns our input filter.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter() {

        // Return input filter if exists
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        // Return value (input filter)
        $retVal =
                $this->inputFilter =
                new InputFilter();

        // Input factory
        $factory = new InputFactory();

        // Screen Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('screen-name', array(
                'name' => 'screenName',
                'required' => false
        ))));

        // Password
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('password', array(
                'name' => 'password',
                'required' => true
        ))));

        // Role
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'role',
                    'required' => false
        ))));

        // Access Group
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'accessGroup',
                    'required' => false
        ))));
        
        // Status
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'status',
                    'required' => false
        ))));
        
        
        // Activation Key
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('activation-key', array(
                    'name' => 'activationKey',
                    'required' => false
        ))));
        
        // Last Login
        // Date Info Id

        $this->inputFilter = $retVal;

        return $retVal;
    }
//
//    /**
//     * Exchange array overriden to divide data between user data, contact data, and date info data
//     * @param array $data
//     * @return \Edm\Model\AbstractModel
//     */
//    public function exchangeArray(array $data) {
//        $contact = $this->getContactProto();
//        $dateInfo = $this->getDateInfoProto();
//        $contactValidKeys = $contact->getValidKeys();
//        $dateInfoValidKeys = $dateInfo->getValidKeys();
//        foreach ($data as $key => $val) {
//            if (in_array($key, $this->validKeys)) {
//                $this->{$key} = $val;
//            }
//            else if (in_array($key, $contactValidKeys)) {
//                $contact->{$key} = $val;
//            }
//            else if (in_array($key, $dateInfoValidKeys)) {
//                $contact->{$key} = $val;
//            }
//        }
//        $this->contactProto = $contact;
//        return $this;
//    }

    /**
     * Gets Contact Proto
     * @param array $data
     * @return Edm\Model\Contact
     */
    public function getContactProto($data = null) {
        if (empty($this->contactProto)) {
            $this->contactProto = new Contact($data);
        }
        return $this->contactProto;
    }
    
    /**
     * Returns our Date Info Proto.
     * @param {mixed|Array|Null} $data
     * @return Edm\Model\DateInfo
     */
    public function getDateInfoProto($data = null) {
        if (empty($this->dateInfoProto)) {
            $this->dateInfoProto = new DateInfo($data);
        }
        return $this->dateInfoProto;
    }
    
}