<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
    Edm\Model\TermTaxonomyProxy,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TermTaxonomyProxyTable extends AbstractTable {

    public function __construct() {
        $this->table = 'term_taxonomies_proxy';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new TermTaxonomyProxy());
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

    public function getById($id) {
        return $this->getBy(array('term_taxonomy_id' => $id));
    }

}
