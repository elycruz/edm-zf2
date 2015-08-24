<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class PostTermRel extends AbstractModel implements InputFilterAwareInterface {

    /**
     * Valid keys for model
     * @var array
     */
    public $validKeys = array(
        'post_id',
        'term_taxonomy_id',
    );
    
    public $notAllowedForUpdate = array('post_id');

    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;

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
                    'required' => true
                )
        )));
        
        // Post Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'post_id',
                    'required' => false
                )
        )));
        
        $this->inputFilter = $retVal;

        return $retVal;
    }
    
}