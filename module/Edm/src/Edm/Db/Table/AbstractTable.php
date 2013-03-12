<?php

namespace Edm\Db\Table;

use Zend\Db\TableGateway\TableGateway,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Edm\Db\DbDataHelperAware,
    Edm\TraitPartials\ServiceLocatorAwareTrait,
    Edm\TraitPartials\DbDataHelperAwareTrait;

abstract class AbstractTable extends TableGateway 
implements DbDataHelperAware, ServiceLocatorAwareInterface {
    
    use DbDataHelperAwareTrait,
    ServiceLocatorAwareTrait;
    
    public $dbDataHelper;
    public $serviceLocator;
    
    public function getBy(array $by) {
        $row = $this->select($by)->current();
        if (!empty($row)) {
            $copy = $this->getDbDataHelper()->reverseEscapeTuple($row->toArray());
            $row->exchangeArray($copy);
        }
        return $row;
    }
}

