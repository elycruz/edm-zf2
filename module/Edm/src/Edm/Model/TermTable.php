<?php
namespace Edm\Model;

use Edm\Model\AbstractTable,
        Edm\Model\Term;

class TermTable extends AbstractTable {

    public function __construct($table, \Zend\Db\Adapter\Adapter $adapter, $features = null, \Zend\Db\ResultSet\ResultSetInterface $resultSetPrototype = null, \Zend\Db\Sql\Sql $sql = null) {
        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }
    public function create (Term $item) {
        return $this->insert($item->toArray());
    }
    
    public function read () {
        return $this->select();
    }
    
    public function getById ($term_id) {
        return $this->getBy(array('term_id' => $term_id));
    }
}
