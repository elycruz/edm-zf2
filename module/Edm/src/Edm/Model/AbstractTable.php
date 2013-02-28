<?php

namespace Edm\Model;

use Zend\Db\TableGateway\TableGateway,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Edm\Db\DatabaseDataHelper,
    Edm\Db\DbDataHelperAccess,
    Edm\Db\DbDataHelper;

abstract class AbstractTable extends TableGateway 
implements DbDataHelperAccess, ServiceLocatorAwareInterface {

    public $dbDataHelper;
    public $serviceLocator;

    public function getBy(array $by) {
        $row = $this->select($by)->current();
        if (!empty($row)) {
            $copy = $this->getDbDataHelper()->reverseEscapeTuple($row->toArray());
            $row->exchangeArray($copy);
        }
        return $row;
//        if (empty($row)) {
//            $i = 0;
//            $msg = '';
//            foreach ($by as $key => $val) {
//                $msg .= $i != count($by) ? ', ' : '';
//                $msg .= $key . ' => ' . $val;
//                $i += 1;
//            }
//            throw new \Exception('Couldn\'t find row by criteria: ' . $msg);
//        }
//        return $row;
    }

    public function setDbDataHelper(DbDataHelper $dbDataHelper) {
        $this->dbDataHelper = $dbDataHelper;
    }

    public function getDbDataHelper() {
        if (empty($this->dbDataHelper)) {
            $this->dbDataHelper = new DatabaseDataHelper();
//                    $this->getServiceLocator()->get('Edm\Db\DatabaseDataHelper');
        }
        return $this->dbDataHelper;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}

