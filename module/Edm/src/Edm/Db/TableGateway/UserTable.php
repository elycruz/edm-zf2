<?php

namespace Edm\Db\TableGateway;

use Edm\Db\Table\BaseTableGateway,
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class UserTable extends BaseTableGateway {

    protected $alias = 'user';
    
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
        $this->insert($data);
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function updateItem($id, array $data) {
        $this->update($data, array('user_id' => $id));
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function deleteItem($id) {
        return $this->delete(array('user_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getFirstBy(array('user_id' => $id));
    }
    
    public function updateLastLoginForId ($id) {
        $today = new \Zend\Stdlib\DateTime();
        $this->updateItem($id, array('lastLogin' => $today->getTimestamp()));
    }
    
}
