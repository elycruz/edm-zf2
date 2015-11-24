<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class DateInfoProto extends AbstractProto {

    protected $_allowedKeysForProto = array(
        'date_info_id',
        'createdDate',
        'createdById',
        'lastUpdated',
        'lastUpdatedById',
        'checkedInDate',
        'checkedInById',
        'checkedOutDate',
        'checkedOutById',
    );

    protected $_notAllowedKeysForUpdate = array(
        'date_info_id'
    );

    protected $_formKey = 'dateInfo';

    public function getInputFilter() {
        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }
        
        $retVal = new InputFilter();
        $factory = new InputFactory();

        // Last Updated 
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'lastUpdated',
                    'required' => false
        ))));
        
        // Last Updated By Id
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'lastUpdatedById',
                    'required' => false
        ))));
        
        // Checked In Date
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'checkedInDate',
                    'required' => false
        ))));
        
        // Checked Out Date
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'checkedOutDate',
                    'required' => false
        ))));
        
        // Checked Out By Id
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'checkedOutById',
                    'required' => false
        ))));

        $this->_inputFilter = $retVal;

        return $this->_inputFilter;
    }

}
