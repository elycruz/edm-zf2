<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class ContactProto extends AbstractProto {

    /**
     * Contact type
     * @var string
     */
    public $type = 'user';

    /**
     * @var string
     */
    public $userParams = '';
    
    /**
     * Allowed keys for db and for proto.
     * @var array
     */
    protected $_allowedKeysForProto = array(
        'contact_id',
        'parent_id',
        'name',
        'firstName',
        'middleName',
        'lastName',
        'email',
        'altEmail',
        'type',
        'userParams'
    );

    /**
     * @var array
     */
    protected $_notAllowedKeysForUpdate = array(
        'contact_id',
        'email'
    );

    /**
     * @var string
     */
    protected $_formKey = 'contact';

    public function getInputFilter() {

        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }

        $retVal = new InputFilter();
        $factory = new InputFactory();

        // Parent Id
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('id', array(
                'name' => 'parent_id',
                'required' => false
        ))));

        // Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('name', array(
                'name' => 'name',
                'required' => false
        ))));

        // First Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-name', array(
                'name' => 'firstName',
                'required' => false
        ))));
        
        // Middle Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('name', array(
                'name' => 'middleName',
                'required' => false
        ))));
        
        // Last Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-name', array(
                'name' => 'lastName',
                'required' => false
        ))));
        
        // Email
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('email', array(
                'name' => 'email',
                'required' => true
        ))));
        
        // Alternate Email
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('email', array(
                'name' => 'altEmail',
                'required' => false
        ))));

        // Type
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                'name' => 'type',
                'required' => false
        ))));
        
//         User Params
//        $retVal->add($factory->createInput(
//            self::getDefaultInputOptionsByKey('short-alias', array(
//                'name' => 'contact-type',
//                'required' => false
//        ))));

        $this->_inputFilter = $retVal;

        return $retVal;
    }

}
