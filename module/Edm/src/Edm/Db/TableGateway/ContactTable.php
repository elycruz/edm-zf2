<?php

namespace Edm\Db\TableGateway;

use Edm\Db\Table\BaseTableGateway,
    Edm\Model\Contact,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class ContactTable extends BaseTableGateway {
    
    protected $alias = 'contact';
    
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
        $this->insert($data);
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function updateItem($id, array $data) {
        $this->update($data, array('contact_id' => $id));
        return $this->getAdapter()->getDriver()->getLastGeneratedValue();
    }

    public function deleteItem($id) {
        return $this->delete(array('contact_id' => $id));
    }

    public function read() {
        return $this->select();
    }

    public function getById($id) {
        return $this->getFirstBy(array('contact_id' => $id));
    }
    
}
