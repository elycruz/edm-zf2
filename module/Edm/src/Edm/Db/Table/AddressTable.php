<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
//    Zend\Db\Adapter\Adapter,
//    Zend\Db\ResultSet\ResultSetInterface,
//    Zend\Db\Sql\Sql
    Edm\Model\Address,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;;

class AddressTable extends AbstractTable {

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

    public function createItem(array $data) {
        return $this->insert($data);
    }

    public function updateItem($id, array $data) {
        return $this->update($data, array('address_id' => $id));
    }

    public function deleteItem($id) {
        return $this->delete(array('address_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getBy(array('address_id' => $id));
    }
    
}
