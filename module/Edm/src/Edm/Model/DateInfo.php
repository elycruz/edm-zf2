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

    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }
        
        $inputFilter = new InputFilter();
        
        $factory = new InputFactory();
        
        return $this->inputFilter = $inputFilter;
    }

}