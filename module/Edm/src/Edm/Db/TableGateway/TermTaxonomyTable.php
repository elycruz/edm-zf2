<?php

namespace Edm\Db\TableGateway;

use Edm\Db\Table\BaseTableGateway,
    Edm\Model\TermTaxonomy,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TermTaxonomyTable extends BaseTableGateway {
    
    protected $alias = 'termTaxonomy';
    
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
        $this->insert($data);
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function updateItem($id, array $data) {
        $this->update($data, array('term_taxonomy_id' => $id));
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function deleteItem($id) {
        return $this->delete(array('term_taxonomy_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getFirstBy(array('term_taxonomy_id' => $id));
    }
    
}
