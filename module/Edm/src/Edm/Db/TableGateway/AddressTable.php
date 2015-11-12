<?php

namespace Edm\Db\TableGateway;

use BaseTableGateway,
//    Zend\Db\Adapter\Adapter,
//    Zend\Db\ResultSet\ResultSetInterface,
//    Zend\Db\Sql\Sql
    Edm\Model\Address,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;;

class AddressTable extends BaseTableGateway {

    protected $alias = 'address';
    
    public function __construct() {
        $this->table = 'addresses';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new Address());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    // `createItem` already defined by parent class

    public function updateItem($id, array $data) {
        return $this->update($data, array('address_id' => $id));
    }

    public function deleteItem($id) {
        return $this->delete(array('address_id' => $id));
    }

    public function getById($id) {
        return $this->getFirstBy(array('address_id' => $id));
    }
    
}
