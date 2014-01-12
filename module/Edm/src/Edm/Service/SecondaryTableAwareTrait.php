<?php

/**
 * Description of SecondaryTableAwareTrait
 *
 * @author ElyDeLaCruz
 */
class SecondaryTableAwareTrait {

    protected $secondaryTable = null;
    protected $secondaryTableClassName = null;
    protected $secondaryTableAlias = null;
    protected $defaultSecondaryTableAlias = 'secTable';
    protected $secondaryTableTypeFieldName = 'type';
    protected $secondaryProtoClassName = '';
    protected $protoClassName = '';

    public function getSecondaryTable() {
        if (empty($this->secondaryTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->secondaryTable = new TableGateway(
                    $this->getSecondaryTableClassName(), $this->getDb(), $feature);
        }
        return $this->secondaryTable;
    }

    public function getSecondaryTableAlias() {
        if (empty($this->secondaryTableAlias)) {
            $this->secondaryTableAlias = $this->defaultSecondaryTableAlias;
        }
        return $this->secondaryTableAlias;
    }

    public function setSecondaryTableAlias($alias) {
        $this->secondaryTableAlias = $alias;
        return $this;
    }

    public function getSecondaryTableClassName() {
        return $this->secondaryTableClassName;
    }

    public function setSecondaryTableClassName($name) {
        $this->secondaryTableClassName = $name;
        return $this;
    }

    /**
     * Sets the result set object's prototype object and the 
     * secondary prototype's  class name.  
     * Expects a class string; I.e., Edm\Model\HelloWorld
     * @param string $name
     * @return \Edm\Service\AbstractService
     */
    public function __setSecondaryProtoClassName($name) {
        $rowProto = new $this->protoClassName();
        $rowProto->setSecondaryProtoName($name);
        $this->resultSet->setArrayObjectPrototype($rowProto);
        return $this;
    }

    public function getProtoClassName() {
        
    }

    public function getSecondaryTableTypeFieldName() {
        return $this->secondaryTableTypeFieldName;
    }

    public function setSecondaryTableTypeFieldName($secondaryTableTypeFieldName) {
        $this->secondaryTableTypeFieldName = $secondaryTableTypeFieldName;
        return $this;
    }
    
    /**
     * Clear all secondary table relationships within this service
     * and Set the result set object of this service to it's default
     * @return Edm\Service\ViewModuleService
     */
    public function clearSecondaryTableRelationship() {
        unset($this->secondaryTable);
        unset($this->secondaryTableClassName);
        unset($this->secondaryTableAlias);
        $this->secondaryTableAlias = $this->defaultSecondaryTableAlias;
        $this->resultSet->setArrayObjectPrototype(new ViewModule());
        return $this;
    }

}
