<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\TraitPartials;

use Edm\Db\DatabaseDataHelper,
 Edm\Db\DbDataHelper;

/**
 * Description of DbDataHelperTrait
 *
 * @author ElyDeLaCruz
 */
trait DbDataHelperAwareTrait {
    public function setDbDataHelper(DbDataHelper $dbDataHelper) {
        $this->dbDataHelper = $dbDataHelper;
    }

    public function getDbDataHelper() {
        if (empty($this->dbDataHelper)) {
            $this->dbDataHelper = new DatabaseDataHelper();
        }
        return $this->dbDataHelper;
    }
}
