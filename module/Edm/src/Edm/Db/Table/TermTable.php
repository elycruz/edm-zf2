<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
    Edm\Model\Term,
    Zend\Db\ResultSet\ResultSet,
//    Zend\Db\Adapter\Adapter,
//    Zend\Db\ResultSet\ResultSetInterface,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
//    Zend\Db\Sql\Sql;

class TermTable extends AbstractTable {

    protected $alias = 'term';
    
    public function __construct() {
        $this->table = 'terms';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new Term());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        return $this->insert($data);
    }

    public function updateItem($alias, array $data) {
        return $this->update($data, array('alias' => $alias));
    }

    public function deleteItem($alias) {
        return $this->delete(array('alias' => $alias));
    }

    public function read() {
        return $this->select();
    }

    public function getByAlias($alias) {
        return $this->getBy(array('alias' => $alias));
    }

}
