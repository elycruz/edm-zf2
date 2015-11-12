<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;


/**
 * Description of Page
 * @author ElyDeLaCruz
 */
class Page extends AbstractModel implements InputFilterAwareInterface {

    protected $validKeys = array(
        'page_id',
        'type',
        'uri',
        'label',
        'alias',
        'visible',
        'htmlAttribs',
//        'html_id',
//        'html_class',
//        'html_title',
//        'html_rel',
//        'html_rev',
//        'html_target',
//        'fragment',
        'description',
        'parent_id',
        'acl_resource',
        'acl_privilege',
        'mvc_action',
        'mvc_controller',
        'mvc_module',
        'mvc_route',
        'mvc_resetParamsOnRender',
        'mvc_params',
        'userParams'
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

        // Page Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'page_id',
                            'required' => false)
        )));

        // Parent Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('id', array(
                            'name' => 'parent_id',
                            'required' => false)
        )));

        // Type
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'type',
                            'required' => false)
        )));

        // Label
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'label',
                            'required' => false)
        )));

        // Alias
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'alias',
                            'required' => true)
        )));

        // Visibble
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('int', array(
                            'name' => 'visible',
                            'required' => false)
        )));

        // Html Id
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'html_id',
                            'required' => false)
        )));

        // Html Class
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'html_class',
                            'required' => false
        ))));

        // Html Title
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'html_title',
                            'required' => false
        ))));
        
        // Html Rel
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'html_rel',
                            'required' => false
        ))));
        
        // Html Rev
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'html_rev',
                            'required' => false
        ))));
        
        // Html Target
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('short-alias', array(
                            'name' => 'html_target',
                            'required' => false
        ))));
        
        // Description
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('description', array(
                            'name' => 'description',
                            'required' => false
        ))));
        
        // Acl Resource
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'acl_resource',
                            'required' => false
        ))));
        
        // Acl Privilege
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'acl_privilege',
                            'required' => false
        ))));
        
        // Mvc Action
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'mvc_action',
                            'required' => false
        ))));
        
        // Mvc Controller
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'mvc_controller',
                            'required' => false
        ))));
        
        // Mvc Module
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'mvc_module',
                            'required' => false
        ))));
        
        // Mvc Route
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('alias', array(
                            'name' => 'mvc_route',
                            'required' => false
        ))));
        
        // Mvc Reset Params on Render
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('boolean', array(
                            'name' => 'mvc_resetParamsOnRender',
                            'required' => false
        ))));
        
        // Mvc Reset Params on Render
        $retVal->add($factory->createInput(
                        self::getDefaultInputOptionsByKey('name', array(
                            'name' => 'mvc_params',
                            'required' => false
        ))));

        $this->inputFilter = $retVal;

        return $retVal;
    }

    /**
     * Mixed Term Rel Proto
     * @return Edm\Model\MixedTermRel
     */
    public function getMixedTermRelProto($data = null) {
        if (empty($this->mixedTermRelProto)) {
            $this->mixedTermRelProto = new MixedTermRel($data);
        }
        return $this->mixedTermRelProto;
    }
    
    /**
     * Menu Page Rel Proto
     * @param {array} $data - default `null`
     * @return Edm\Model\MenuPageRel
     */
    public function getMenuPageRelProto ($data = null) {
        if (empty($this->menuPageRelProto)) {
            $this->menuPageRelProto = new MenuPageRel($data);
        }
        return $this->menuPageRelProto;    
    }

}
