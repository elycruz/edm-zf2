<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\Contact,
    Edm\Model\AbstractModel;

class User extends AbstractModel implements InputFilterAwareInterface {

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
        'registeredDate',
        'registeredById',
        'lastUpdated',
        'lastUpdatedById',
        'checkedInDate',
        'checkedOutDate',
        'checkedOutById',
        'contact_id'
    );
    
    public $notAllowedForUpdate = array(
        'activationKey',
        'registeredDate',
        'registeredBy',
        'contact_id',
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
    
    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

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
        // Registered Date
        // Registered By Id
        // Last Updated 
        // Last Updated By Id
        // Checked In Date
        // Checked Out Date
        // Checked Out By Id

        $this->inputFilter = $retVal;

        return $retVal;
    }
    
    
    /**
     * Exchange array overriden to divide data between user data and contact data
     * @param array $data
     * @return \Edm\Model\AbstractModel
     */
    public function exchangeArray(array $data) {
        $contact = $this->getContactProto();
        $contactValidKeys = $contact->getValidKeys();
        foreach ($data as $key => $val) {
            if (in_array($key, $this->validKeys)) {
                $this->{$key} = $val;
            }
            else if (in_array($key, $contactValidKeys)) {
                $contact->{$key} = $val;
            }
        }
        $this->contactProto = $contact;
        return $this;
    }
    
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
    
}