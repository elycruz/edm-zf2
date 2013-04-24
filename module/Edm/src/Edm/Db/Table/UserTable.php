<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class UserTable extends AbstractTable {
    
    public function __construct() {
        $this->table = 'users';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new User());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        return $this->insert($data);
    }

    public function updateItem($id, array $data) {
        return $this->update($data, array('user_id' => $id));
    }

    public function deleteItem($id) {
        return $this->delete(array('user_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getBy(array('user_id' => $id));
    }
    
}
