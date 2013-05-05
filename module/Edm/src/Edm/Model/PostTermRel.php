<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class PostTermRel extends AbstractModel implements InputFilterAwareInterface {

    public $accessGroup = 'cms-manager';
    
    public $status      = 'draft';
    
    public $type        = 'post';
    
    /**
     * Valid keys for model
     * @var array
     */
    public $validKeys = array(
        'post_id',
        'term_taxonomy_id',
        'accessGroup',
        'status',
        'type'
    );
    
    public $notAllowedForUpdate = array('type');

    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;
    
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
        
        // Term Taxonomy Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'term_taxonomy_id',
                    'required' => false
                )
        )));
        
        // Post Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'post_id',
                    'required' => false
                )
        )));
        
        // Type
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'type',
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
        
        $this->inputFilter = $retVal;

        return $retVal;
    }
    
}