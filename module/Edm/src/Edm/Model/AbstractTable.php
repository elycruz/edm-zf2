<?php

namespace Edm\Model;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractTable extends TableGateway {

    public function getBy(array $by) {
        $rowSet = $this->select($by);
        $row = $rowSet->current();
        if (!$row) {
            $i = 0;
            foreach ($by as $key => $val) {
                $msg .= $i != count($by) ? ', ' : '';
                $msg .= $key . ' => ' . $val;
                $i += 1;
            }
            throw new \Exception('Couldn\'t find row by criteria: ' . $msg);
        }
        return $row;
    }
}

