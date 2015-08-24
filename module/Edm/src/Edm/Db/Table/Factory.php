<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/23/2015
 * Time: 9:48 PM
 */

namespace Edm\Db\Table;

use Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class Factory {

    /**
     * Easy way to construct our table gateways from our services.
     * @param $tableName string
     * @return mixed Zend\Db\TableGateway\TableGateway
     */
    public static function factory ($tableName) {
        $featureSet = new FeatureSet();
        $featureSet->addFeature(new GlobalAdapterFeature());
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        return new \Zend\Db\TableGateway\TableGateway ($tableName, $adapter, $featureSet);
    }

}