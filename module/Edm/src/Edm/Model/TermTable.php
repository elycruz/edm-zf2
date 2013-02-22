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
    
    public function create (array $data) {
        return $this->insert($data);
    }
    
    public function read () {
        return $this->select();
    }
    
    public function getByAlias ($alias) {
        return $this->getBy(array('alias' => $alias));
    }
}
