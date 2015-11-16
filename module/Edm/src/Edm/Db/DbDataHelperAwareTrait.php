<?php

namespace Edm\Db;

/**
 * Description of DbDataHelperTrait
 *
 * @author ElyDeLaCruz
 */
trait DbDataHelperAwareTrait {
    
    public $dbDataHelper;

    /**
     * @param \Edm\Db\DbDataHelperInterface $dbDataHelper
     */
    public function setDbDataHelper(DbDataHelperInterface $dbDataHelper) {
        $this->dbDataHelper = $dbDataHelper;
    }

    /**
     * @return \Edm\Db\DbDataHelper
     */
    public function getDbDataHelper() {
        if (empty($this->dbDataHelper)) {
            $this->dbDataHelper = new DbDataHelper();
        }
        return $this->dbDataHelper;
    }
}
