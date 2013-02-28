<?php
namespace Edm\Model;

use Edm\Model\AbstractTable,
        Zend\Db\Adapter\Adapter,
        Zend\Db\ResultSet\ResultSetInterface,
        Zend\Db\Sql\Sql;

class TermTable extends AbstractTable {

    public function __construct($table, Adapter $adapter, $features = null, 
            ResultSetInterface $resultSetPrototype = null, Sql $sql = null) {
        parent::__construct($table, $adapter, $features, 
                $resultSetPrototype, $sql);
    }
    
    public function createItem (array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
        return $this->insert($data);
    }
    
    public function updateItem ($alias, array $data) {
        $data = $this->getDbDataHelper()->escapeTuple($data);
        return $this->update($data, array('alias' => $alias));
    }
    
    public function deleteItem ($alias) {
        return $this->delete(array('alias' => $alias));
    }
    
    public function read () {
        return $this->select();
    }
    
    public function getByAlias ($alias) {
        return $this->getBy(array('alias' => $alias));
    }
}
