<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

/**
 * @todo add filters to this proto
 */
class DateInfo extends AbstractModel implements InputFilterAwareInterface {

    protected $inputFilter = null;
    
    protected $validKeys = array(
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

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }
        
        $retVal = new InputFilter();
        
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
        
        $factory = new InputFactory();
        
        return $this->inputFilter = $retVal;
    }

}