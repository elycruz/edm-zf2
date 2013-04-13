<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
    Edm\Model\Contact,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;;

class TermTaxonomyTable extends AbstractTable {

    
    public function __construct() {
        $this->table = 'contacts';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new Contact());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function createItem(array $data) {
        return $this->insert($data);
    }

    public function updateItem($id, array $data) {
        return $this->update($data, array('contact_id' => $id));
    }

    public function deleteItem($id) {
        return $this->delete(array('contact_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getBy(array('contact_id' => $id));
    }
    
}
