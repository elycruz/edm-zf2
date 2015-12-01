<?php

namespace Edm\Db\TableGateway;

trait DateInfoTableAwareTrait {
    
    protected $dateInfoTable;

    /**
     * @return \Edm\Db\TableGateway\DateInfoTable
     */
    public function getDateInfoTable() {
        if (empty($this->dateInfoTable)) {
            $this->dateInfoTable =
                $this->getServiceLocator()
                     ->get('Edm\Db\TableGateway\DateInfoTable');
        }
        return $this->dateInfoTable;
    }
}
