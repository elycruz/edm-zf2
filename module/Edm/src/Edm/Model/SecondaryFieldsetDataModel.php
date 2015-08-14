<?php

namespace Edm\Model;

/**
 * Description of SecondaryFieldsetDataModel
 * @author ElyDeLaCruz
 */
use Edm\Utils\StringHelpersTrait,
    Zend\Db\TableGateway\TableGatewayInterface,
    Zend\Form\FieldsetInterface;

class SecondaryFieldsetDataModel { 
    
    use StringHelpersTrait; 
    
    private $fieldsetClassNamePrefix = '\\Edm\\Form';
    
    private $fieldsetClassNameSuffix = 'Fieldset';
    
    private $modelClassNamePrefix = '\\Edm\\Model';
    
    private $modelClassNameSuffix = 'Model';
    
    private $classNamePrefix;
    
    private $fieldsetClassName;
    
    private $modelClassName;
    
    private $formActionTypeAppendage = '/type/';
    
    private $model;
    
    private $fieldset;
    
    private $fieldsetAlias;
    
    public function __construct($fieldsetAlias) {
        $this->setFieldset($fieldsetAlias)
             ->populateNames();
    }
    
    public function getFieldsetAlias() {
        return $this->fieldsetAlias;
    }

    public function setFieldsetAlias($fieldsetAlias) {
        $this->fieldsetAlias = $fieldsetAlias;
        return $this;
    }

    public function getClassNamePrefix() {
        return $this->classNamePrefix;
    }

    public function setClassNamePrefix($classNamePrefix) {
        $this->classNamePrefix = $classNamePrefix;
        return $this;
    }

    public function getFieldsetClassName() {
        return $this->fieldsetClassName;
    }

    public function setFieldsetClassName($fieldsetClassName) {
        $this->fieldsetClassName = $fieldsetClassName;
        return $this;
    }

    public function getModelClassName() {
        return $this->modelClassName;
    }

    public function setModelClassName($modelClassName) {
        $this->modelClassName = $modelClassName;
        return $this;
    }

    public function getFormActionTypeAppendage() {
        return $this->formActionTypeAppendage;
    }

    public function setFormActionTypeAppendage($formActionTypeAppendage) {
        $this->formActionTypeAppendage = $formActionTypeAppendage;
        return $this;
    }

    public function getModel() {
        if (empty($this->model)) {
            $modelClassName = $this->getModelClassName();
            if (empty($modelClassName)) {
                $this->populateNames();
                $modelClassName = $this->getModelClassName();
            }
            $this->setModel(new $modelClassName());
        }
        return $this->model;
    }

    public function setModel(TableGatewayInterface $model) {
        $this->model = $model;
        return $this;
    }

    public function getFieldset() {
        if (empty($this->fieldset)) {
            $fieldsetClassName = $this->fieldsetClassName;
            if (empty($fieldsetClassName)) {
                $this->populateNames();
                $fieldsetClassName = $this->getFieldsetClassName();
            }
            $this->setFieldset(new $fieldsetClassName());
        }
        return $this->fieldset;
    }

    public function setFieldset(FieldsetInterface $fieldset) {
        $this->fieldset = $fieldset;
        return $this;
    }
    
    public function populateNames () {
        $this   ->setClassNamePrefix(
                        $this->strToClassCase(
                                $this->getFieldsetAlias()))
                ->setFieldsetClassName(
                        $this->fieldsetClassNamePrefix . '\\' . 
                        $this->getClassNamePrefix() .
                        $this->fieldsetClassNameSuffix)
                ->setModelClassName(
                        $this->modelClassNamePrefix . '\\' .
                        $this->getClassNamePrefix() . 
                        $this->modelClassNameSuffix);
    }


}
