<?php

namespace Edm\Db\TableGateway;

use Edm\Db\Table\BaseTableGateway,
    Edm\Model\Post,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class PostTable extends BaseTableGateway {

    protected $alias = 'post';
    
    public function __construct() {
        $this->table = 'posts';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new Post());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        $this->insert($data);
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function updateItem($id, array $data) {
        $this->update($data, array('post_id' => $id));
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function deleteItem($id) {
        return $this->delete(array('post_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getFirstBy(array('post_id' => $id));
    }
    
    
}
