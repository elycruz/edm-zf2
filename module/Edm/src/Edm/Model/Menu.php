<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel,
    Edm\Model\ViewModuleProtoAwareTrait;

/**
 * Description of Menu
 * @author ElyDeLaCruz
 */
class Menu extends AbstractModel implements InputFilterAwareInterface {

    use ViewModuleProtoAwareTrait;

    protected $validKeys = array(
        'menu_id',
        'view_module_id',
        'parent_id',
        'minDepth',
        'maxDepth',
        'onlyActiveBranch',
        'renderParents',
        'isMainMenu',
        'useModuleHelper',
        'ulClass',
        'menuPartialScript',
        'menuHelperName'
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

        // View Module Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'view_module_id',
                            'required' => false)
        )));

        // Parent Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'parent_id',
                            'required' => false)
        )));

        // Min Depth
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'minDepth',
                            'required' => false)
        )));

        // Max Depth
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'maxDepth',
                            'required' => false)
        )));

        // Only Active Branch
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'onlyActiveBranch',
                            'required' => false)
        )));

        // Render Parents
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'renderParents',
                            'required' => false)
        )));

        // Is Main Menu
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'isMainMenu',
                            'required' => false)
        )));

        // Use Module Helper
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'useModuleHelper',
                            'required' => false)
        )));

        // Unordered List Clas
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'ulClass',
                            'required' => false)
        )));

        // Menu Parial Script
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'menuPartialScript',
                            'required' => false
        ))));

        // Menu Helper Name
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'menuHelperName',
                            'required' => false
        ))));

        $this->inputFilter = $retVal;

        return $retVal;
    }

}
