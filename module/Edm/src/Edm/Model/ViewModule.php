<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel,
    Edm\Model\MixedTermRel;

/**
 * Description of ViewModule
 *
 * @author ElyDeLaCruz
 */
class ViewModel extends AbstractModel implements InputFilterAwareInterface {

    public $userParams = '';
    
    public $content = '';
    
    /**
     * Mixed Term Rel Proto
     * @var Edm\Model\AbstractModel
     */
    protected $mixedTermRelProto;

    /**
     * Valid keys for this model
     * @var array
     */
    protected $validKeys = array(
        'view_module_id',
        'title',
        'alias',
        'content',
        'type',
        'helperName',
        'helperType',
        'partialScript',
        'createdById',
        'createdDate',
        'lastUpdatedById',
        'lastUpdated',
        'checkedOutById',
        'checkedOutDate',
        'checkedInDate',
        'allowedOnPages',
        'userParams'
    );

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

    /**
     * Sets our input filter
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    /**
     * Returns our input filter
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

        // View Module Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'view_module_id',
                            'required' => false
                                )
        )));

        // Title
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'title',
                            'required' => true
        ))));

        // Alias
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'alias',
                            'required' => false
        ))));

        // Content
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('description', array(
                            'name' => 'content',
                            'required' => false
        ))));

        // Type
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'type',
                            'required' => false
        ))));

        // Helper Name
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'helperName',
                            'required' => false
        ))));

        // Helper Type
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'helperType',
                            'required' => false
        ))));

        // Partial Script
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'partialScript',
                            'required' => false
        ))));

        // List Order
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'listOrder',
                            'required' => false
        ))));

        // Status (using post status taxonomy for this one)
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'status',
                            'required' => false
        ))));

        // Access Group
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'accessGroup',
                            'required' => false
        ))));

        // Created Date
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'createdDate',
                            'required' => false
        ))));

        // Created By Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'createdById',
                            'required' => false
        ))));

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

        // User Params

        $this->inputFilter = $retVal;

        return $retVal;
    }

    /** 
     * Mixed Term Rel Proto
     * @return Edm\Model\MixedTermRel
     */
    public function getMixedTermRelProto() {
        if (empty($this->mixedTermRelProto)) {
            $this->mixedTermRelProto = new MixedTermRel();
        }
        return $this->mixedTermRelProto;
    }

}
