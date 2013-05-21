<?php

// @todo refactor edm\db\table to not be abstract and to not use service locator or db data helper 
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
    
    /**
     * Table Alias 
     * ** Should be the same as the extending classes name camel cased 
     * without the trailing "Table";  I.e.,  for the "UserTable" class the 
     * alias should be "user" this allows services to get tables by alias and
     * allows sql to have an oop nature by having columns that point to these
     * tables by aliases as well.
     * @var string
     */
    protected $alias;
    
    public function getBy(array $by) {
        return $this->select($by)->current();
//        if (!empty($row)) {
//            $copy = $this->getDbDataHelper()->reverseEscapeTuple($row->toArray());
//            $row->exchangeArray($copy);
//        }
//        return $row;
    }
}

