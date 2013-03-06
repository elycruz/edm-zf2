<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
//    Zend\Db\Adapter\Adapter,
//    Zend\Db\ResultSet\ResultSetInterface,
//    Zend\Db\Sql\Sql
    Edm\Model\TermTaxonomy,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;;

class TermTaxonomyTable extends AbstractTable {

    public function __construct() {
        $this->table = 'term_taxonomies';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new TermTaxonomy());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
        return $this->insert($data);
    }

    public function updateItem($alias, array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
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
