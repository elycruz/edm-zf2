<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\FieldsInFormAwareTrait,
    Edm\Model\FieldsInFormAwareInterface,
    Edm\Model\AbstractModel;

/**
 * Description of Menu
 * @author ElyDeLaCruz
 */
class MenuPageRel extends AbstractModel 
implements InputFilterAwareInterface, FieldsInFormAwareInterface {

    use FieldsInFormAwareTrait;

    protected $validKeys = array(
        'menu_id',
        'page_id'
    );

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

        // Menu Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'menu_id',
                            'required' => false)
        )));

        // Page Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'page_id',
                            'required' => false)
        )));

        $this->inputFilter = $retVal;

        return $retVal;
    }

}
