<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
//    Zend\Db\Adapter\Adapter,
//    Zend\Db\ResultSet\ResultSetInterface,
//    Zend\Db\Sql\Sql
    Edm\Model\User,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;;

class TermTaxonomyTable extends AbstractTable {

    
    public function __construct() {
        $this->table = 'user';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new User());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
        return $this->insert($data);
    }

    public function updateItem($id, array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
        return $this->update($data, array('term_taxonomy_id' => $id));
    }

    public function deleteItem($id) {
        return $this->delete(array('term_taxonomy_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getBy(array('term_taxonomy_id' => $id));
    }
    
}
