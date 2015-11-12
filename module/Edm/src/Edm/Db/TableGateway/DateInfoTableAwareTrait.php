<?php

namespace Edm\Db\TableGateway;

use Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

/**
 * Description of DateInfoTableAwareTrait
 * @author ElyDeLaCruz
 */
trait DateInfoTableAwareTrait {
    
    protected $dateInfoTable;
        
    public function getDateInfoTable() {
        if (empty($this->dateInfoTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->dateInfoTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'date_info', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->dateInfoTable;
    }
}
